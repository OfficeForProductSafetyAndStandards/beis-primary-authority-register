<?php

namespace Drupal\par_data\Entity;

/**
 * The interface for the base PAR entity type class.
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

  /**
   * Get the fields required to complete this entity.
   *
   * @param bool $include_required
   *   Whether to automatically include required fields.
   *
   * @return null|mixed[]
   *   An array of field names.
   */
  public function getCompletionFields($include_required);

  /**
   * Gets the 'off' or 'on' label for a boolean field.
   *
   * @param string $field_name
   *   The name of the field to load the label for.
   * @param bool $value
   *   Whether to get the 'off' or 'on' label.
   *
   * @return string|bool
   *   The label string if found.
   */
  public function getBooleanFieldLabel($field_name, bool $value);

  /**
   * Get the allowed values for a field.
   *
   * @param string $field_name
   *   The name of the field to load the label for.
   *
   * @return array
   *   An array of key values or empty if none.
   */
  public function getAllowedValues($field_name);

  /**
   * Gets the label for a field given a list of allowed values.
   *
   * @param string $field_name
   *   The name of the field to load the label for.
   * @param $value
   *   The key to look up the label for.
   *
   * @return string
   *   The label string if found, otherwise the original value.
   */
  public function getAllowedFieldlabel($field_name, $value);

}
