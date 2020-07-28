<?php

namespace Drupal\par_data;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\trance\TranceStorage;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the storage class for flows.
 *
 * This extends the base storage class adding extra
 * entity loading mechanisms.
 */
class ParDataStorage extends TranceStorage {

  protected $parDataManager;

  public function __construct(\Drupal\Core\Entity\EntityTypeInterface $entity_type, \Drupal\Core\Database\Connection $database, EntityFieldManagerInterface $entity_field_manager, \Drupal\Core\Cache\CacheBackendInterface $cache, \Drupal\Core\Language\LanguageManagerInterface $language_manager) {
    parent::__construct($entity_type, $database, $entity_field_manager, $cache, $language_manager);

    $this->parDataManager = \Drupal::service('par_data.manager');
  }

  /**
   * Hard delete all PAR Data Entities.
   */
  public function destroy(array $entities) {
    parent::delete($entities);
  }

  /**
   * Modification of entity query allows deleted entities to be excluded.
   *
   * {@inheritDoc}
   */
//  public function getQuery($conjunction = 'AND') {
//    $query = parent::getQuery($conjunction);
//
//    // Do not return deleted entities.
//    $query->condition(ParDataEntity::DELETE_FIELD, 1, '<>');
//
//    return $query;
//  }

  /**
   * Modification of entity query allows deleted entities to be excluded.
   *
   * {@inheritDoc}
   */
//  public function getAggregateQuery($conjunction = 'AND') {
//    $query = parent::getAggregateQuery($conjunction);
//
//    // Do not return deleted entities.
//    $query->condition(ParDataEntity::DELETE_FIELD, 1, '<>');
//
//    return $query;
//  }

  /**
   * Soft delete all PAR Data entities.
   *
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    parent::delete($entities);
  }

  /**
   * Add some default options to newly created entities.
   *
   * {@inheritdoc}
   */
  public function create(array $values = []) {
    $bundle = isset($values['type']) ? $values['type'] : NULL;
    $bundle_entity = \Drupal::service('par_data.manager')->getParBundleEntity($this->entityTypeId, $bundle);

    // Set the type if not already set.
    $values['type'] = $bundle_entity && isset($values['type']) ? $values['type'] : $bundle_entity->id();

    // Set the default status (as the first allowed_status value configured).
    $status_field = $bundle_entity->getConfigurationElementByType('entity', 'status_field');
    $allowed_statuses = $bundle_entity->getAllowedValues($status_field);
    if (isset($status_field) && empty($values[$status_field]) && !empty($allowed_statuses)) {
      $values[$status_field] = key($allowed_statuses);
    }

    // Clear all empty values.
    $values = NestedArray::filter($values);

    return parent::create($values);
  }

  /**
   * Ensure that all the required properties are validated.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   */
  public function validate(EntityInterface $entity) {
    $field_definitions = $this->entityFieldManager->getFieldDefinitions($entity->getEntityTypeId(), $entity->bundle());
    foreach ($field_definitions as $field_name => $field_definition) {
      $storage = $field_definition->getFieldStorageDefinition();
      // Validate all base fields that have been set as required.
      if ($storage->isBaseField() && $field_definition->isRequired()
        && $entity->get($field_definition->getName())->isEmpty()) {
        $params = ['@field' => $field_name, '@entity' => $entity->getEntityTypeId()];
        throw new ParDataException($this->t('The field @field is required for entity @entity', $params));
      }
    }
  }

  /**
   * Save entity.
   * This function deletes relationship cache for the new/updated entity refs.
   *
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity) {
    // Ensure the entity is ready for saving.
    $this->validate($entity);

    // Lowercase all email addresses, these are used in some aggregate functions
    // and should not be upper case.
    if ($entity instanceof ParDataPersonInterface && $entity->hasField('email')) {
      foreach ($entity->get('email') as $delta => $field_item) {
        if (isset($field_item->getValue()['value'])) {
          $email = mb_strtolower($field_item->getValue()['value']);
          $field_item->setValue($email);
        }
        $entity->get('email')->set($delta, $field_item);
      }
    }

    // Ensure that a format value is selected for all text_long field values.
    // @SEE PAR-1618: Text formats not being correctly set or retrieved.
    foreach ($entity->getFieldDefinitions() as $definition) {
      if ($definition->getType() === 'text_long' && !$entity->get($definition->getName())->isEmpty()) {
        foreach ($entity->get($definition->getName()) as $delta => $value) {
          if ($value->get('format')->getValue() === NULL) {
            $entity->get($definition->getName())->get($delta)->get('format')->setValue('plain_text');
          }
        }
      }
    }

    // Load the original entity if it already exists.
    if ($this->has($entity->id(), $entity) && !isset($entity->original)) {
      $entity->original = $this->loadUnchanged($entity->id());
    }

    // Get relationships for the entity being saved.
    $relationships = $entity->getRelationships();

    // Loop through relationships and delete appropriate relationship cache records.
    foreach ($relationships as $uuid => $relationship) {
      // Delete cache record for new/updated references.
      $hash_key = "par_data_relationships:{$relationship->getEntity()->uuid()}";
      \Drupal::cache('data')->delete($hash_key);
    }

    $original = $entity->original;
    if ($entity->getRawStatus() && !$entity->isNew() && isset($original) && $entity->getRawStatus() !== $original->getRawStatus()) {
      // Dispatch the an event for every par entity that has a status update.
      $event = new ParDataEvent($entity);
      $event_to_dispatch = ParDataEvent::statusChange($entity->getEntityTypeId(), $entity->getRawStatus());
      $dispatcher = \Drupal::service('event_dispatcher');
      $dispatcher->dispatch($event_to_dispatch, $event);
    }

    return parent::save($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function doPostSave(EntityInterface $entity, $update) {
    parent::doPostSave($entity, $update);

    // Warm caches.
    $entity->getRelationships();
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids = NULL) {
    $entities = parent::loadMultiple($ids);

    // Do not return any deleted entities.
    // @see PAR-1462 - Removing all deleted entities from loading.
//    $entities = array_filter($entities, function ($entity) {
//      return (!$entity->isDeleted());
//    });

    return $entities;
  }

}
