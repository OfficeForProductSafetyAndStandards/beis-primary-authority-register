<?php

namespace Drupal\par_data;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\trance\TranceStorage;

/**
 * Defines the storage class for flows.
 *
 * This extends the base storage class adding extra
 * entity loading mechanisms.
 */
class ParDataStorage extends TranceStorage {

  /**
   * PAR Data Manager.
   *
   * @var parDataManager
   *   Implements PAR Data Manager.
   */
  protected $parDataManager;

  public function __construct(EntityTypeInterface $entity_type, Connection $database, EntityFieldManagerInterface $entity_field_manager, CacheBackendInterface $cache, LanguageManagerInterface $language_manager, MemoryCacheInterface $memory_cache, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct(
      $entity_type,
      $database,
      $entity_field_manager,
      $cache,
      $language_manager,
      $memory_cache,
      $entity_type_bundle_info,
      $entity_type_manager
    );

    $this->parDataManager = \Drupal::service('par_data.manager');
  }

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
    $bundle = $values['type'] ?? NULL;
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
   * Save entity.
   *
   * {@inheritdoc}
   *    This function deletes relationship cache for on new/updated entity refs.
   */
  public function save(EntityInterface $entity) {
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

    // Loop through relationships and delete appropriate relationship
    // cache records.
    foreach ($relationships as $uuid => $relationship) {
      // Delete cache record for new/updated references.
      $hash_key = "relationships:{$relationship->getEntity()->uuid()}";
      \Drupal::cache('par_data')->delete($hash_key);
    }

    // Identify whether to dispatch a status update.
    $dispatch_status_update = (
      $entity->getRawStatus() &&
      !$entity->isNew() &&
      isset($entity->original) &&
      $entity->getRawStatus() !== $entity->original->getRawStatus()
    );

    $saved = parent::save($entity);

    // Dispatch must happen after the entity is saved.
    if ($dispatch_status_update) {
      // Dispatch an event for every par entity that has a status update.
      $event = new ParDataEvent($entity);
      $event_to_dispatch = ParDataEvent::statusChange($entity->getEntityTypeId(), $entity->getRawStatus());
      $dispatcher = \Drupal::service('event_dispatcher');
      $dispatcher->dispatch($event, $event_to_dispatch);
    }

    return $saved;
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

    return $entities;
  }

}
