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
   * Soft delete all PAR Data entities.
   *
   * {@inheritdoc}
   */
  public function delete(array $entities) {
    if (!$entities) {
      // If no IDs or invalid IDs were passed, do nothing.
      return;
    }

    // Ensure that the entities are keyed by ID.
    $keyed_entities = [];
    foreach ($entities as $entity) {
      $keyed_entities[$entity->id()] = $entity;
    }

    // Perform the delete and reset the static cache for the deleted entities.
    $this->doDelete($keyed_entities);
    $this->resetCache(array_keys($keyed_entities));
  }

  /**
   * {@inheritdoc}
   */
  protected function doDelete($entities) {
    foreach ($entities as $id => $entity) {
      $entity->set(ParDataEntity::DELETE_FIELD, TRUE)->save();
    }
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
