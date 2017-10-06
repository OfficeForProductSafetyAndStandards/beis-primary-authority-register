<?php

namespace Drupal\par_data;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\trance\TranceStorage;

/**
 * Defines the storage class for flows.
 *
 * This extends the base storage class adding extra
 * entity loading mechanisms.
 */
class ParDataStorage extends TranceStorage {

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

    return parent::create($values);
  }
}
