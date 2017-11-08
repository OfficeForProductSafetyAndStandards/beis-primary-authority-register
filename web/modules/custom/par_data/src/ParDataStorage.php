<?php

namespace Drupal\par_data;

use Drupal\Component\Utility\NestedArray;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\trance\TranceStorage;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the storage class for flows.
 *
 * This extends the base storage class adding extra
 * entity loading mechanisms.
 */
class ParDataStorage extends TranceStorage {

  protected $par_data_manager;

  public function __construct(\Drupal\Core\Entity\EntityTypeInterface $entity_type, \Drupal\Core\Database\Connection $database, \Drupal\Core\Entity\EntityManagerInterface $entity_manager, \Drupal\Core\Cache\CacheBackendInterface $cache, \Drupal\Core\Language\LanguageManagerInterface $language_manager) {
    parent::__construct($entity_type, $database, $entity_manager, $cache, $language_manager);

    $this->par_data_manager = \Drupal::service('par_data.manager');
  }

  /**
   * Hard delete all PAR Data Entities.
   */
  public function destroy(array $entities) {
    parent::delete($entities);
  }

  /**
   * Soft delete all PAR Data entities.
   *
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    if (!$entities) {
      // If no IDs or invalid IDs were passed, do nothing.
      return;
    }

    // Perform the delete and key the entities correctly.
    $keyed_entities = [];
    foreach ($entities as $entity) {
      $entity->set(ParDataEntity::DELETE_FIELD, TRUE)->save();
      $keyed_entities[$entity->id()] = $entity;
    }

    // Reset the static cache for the deleted entities.
    $this->resetCache(array_keys($keyed_entities));
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

    // Set the default status (as the first allowed_status value configured).z
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
   * This function deletes relationship cache for the new/updated entity refs.
   *
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity) {
    // Load the original entity if it already exists.
    if ($this->has($entity->id(), $entity) && !isset($entity->original)) {
      $entity->original = $this->loadUnchanged($entity->id());
    }

    // Get relationships for the entity being saved.
    $relationships = $entity->getRelationships();

    // Loop through relationships and delete appropriate cache records.
    foreach ($relationships as $entity_type => $referenced_entities) {
      foreach ($referenced_entities as $referenced_entity_id => $referenced_entity) {
        // Skip existing references - they will already be in the cache.
        if (!empty($entity->original) &&
            !empty($entity->original->getRelationships()[$referenced_entity_id][$referenced_entity->id()])) {
          continue;
        }

        // Delete cache record for new/updated references.
        \Drupal::cache('data')
          ->delete("par_data_relationships:{$entity_type}:{$referenced_entity->id()}");
      }
    }

    // Warm caches.
    $this->par_data_manager->getRelatedEntities($entity);

    return parent::save($entity);
  }
}
