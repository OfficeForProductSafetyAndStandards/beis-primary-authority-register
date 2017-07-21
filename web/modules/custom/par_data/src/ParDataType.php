<?php

namespace Drupal\par_data;

use Drupal\trance\TranceType;

/**
 * The base PAR entity type class.
 *
 */
abstract class ParDataType extends TranceType {

  /**
   * The additional configuration options for this entity.
   *
   * Note: Whether a field is 'required' will be dictated by the field storage.
   *
   * @var array
   */
  public $configuration;

  /**
   * Get the configuration for a given element.
   *
   * @param string $element
   *   The element to retrieve additional configuration for, either a field, property or 'entity'.
   *
   * @return mixed
   */
  public function getConfiguration($element) {
    return isset($this->configuration[$element]) ? $this->configuration[$element] : NULL;
  }

  /**
   * Get the configuration for a given element by type.
   *
   * @param string $element
   *   The element to retrieve additional configuration for, either a field, property or 'entity'.
   * @param string $type
   *   The type of additional configuration to get.
   *
   * @return mixed
   */
  public function getConfigurationByType($element, $type) {
    $element_configuration = $this->getConfiguration($element);
    return isset($element_configuration[$type]['value']) ? $element_configuration[$type]['value'] : NULL;
  }

}
