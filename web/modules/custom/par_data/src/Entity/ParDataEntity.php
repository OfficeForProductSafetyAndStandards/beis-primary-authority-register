<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;

/**
 * Defines the PAR entities.
 *
 * @ingroup par_data
 */
class ParDataEntity extends Trance implements ParDataEntityInterface {

  /**
   * Get bundle entity.
   */
  public function getBundleEntity() {
    $bundle_definition = $this->parDataManager->getEntityBundleDefinition($this->getEntityType());
    $bundle_storage = $this->parDataManager->getEntityTypeStorage($bundle_definition);
    return $bundle = $bundle_storage->load($this->bundle());
  }

  /**
   * {@inheritdoc}
   */
  public function getViewBuilder() {
    return \Drupal::entityTypeManager()->getViewBuilder($this->getEntityTypeId());
  }

  /**
   * {@inheritdoc}
   */
  public function getParStatus() {
    // TODO: Implement getParStatus() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionPercentage() {
    // TODO: Implement getCompletionPercentage() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionFields() {
    // TODO: Implement getCompletionFields() method.
  }

}
