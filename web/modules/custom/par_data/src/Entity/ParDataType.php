<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\TranceType;

/**
 * The base PAR entity type class.
 *
 */
abstract class ParDataType extends TranceType implements ParDataTypeInterface {

  /**
   * The additional configuration options for this entity.
   *
   * Note: Whether a field is 'required' will be dictated by the field storage.
   *
   * @var array
   */
  public $configuration;

  /**
   * {@inheritdoc}
   */
  public function getConfiguration($element) {
    return isset($this->configuration[$element]) ? $this->configuration[$element] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationByType($element, $type) {
    $element_configuration = $this->getConfiguration($element);
    return isset($element_configuration[$type]['value']) ? $element_configuration[$type]['value'] : NULL;
  }

}
