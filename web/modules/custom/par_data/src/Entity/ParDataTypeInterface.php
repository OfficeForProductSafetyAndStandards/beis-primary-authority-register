<?php

namespace Drupal\par_data\Entity;

/**
 * The interface for the base PAR entity type class.
 *
 */
interface ParDataTypeInterface {

  /**
   * Get the configuration for this entity type.
   *
   * @return array
   */
  public function getConfiguration();

  /**
   * Get the configuration for a given element.
   *
   * @param string $element
   *   The element to retrieve additional configuration for, either a field, property or 'entity'.
   *
   * @return array
   */
  public function getConfigurationElement($element);

  /**
   * Get the configuration for this entity type by configuration type.
   *
   * @param string $type
   *   The type of additional configuration to get.
   *
   * @return array
   */
  public function getConfigurationByType($type);

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
  public function getConfigurationElementByType($element, $type);

}
