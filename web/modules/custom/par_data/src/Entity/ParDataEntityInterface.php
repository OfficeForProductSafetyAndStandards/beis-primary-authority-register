<?php

namespace Drupal\par_data\Entity;

/**
 * The interface for PAR entities.
 *
 * @ingroup par_data
 */
interface ParDataEntityInterface {

  /**
   * Get the view builder for the entity.
   *
   * @return \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  public function getViewBuilder();

  /**
   * Return the value of the status field.
   *
   * @return NULL|mixed
   *   The value of the status field.
   */
  public function getParStatus();

  /**
   * Get the fields required to complete this entity.
   *
   * @param boolean $include_required
   *   Whether to automatically include required fields.
   *
   * @return NULL|mixed[]
   *   An array of field names.
   */
  public function getCompletionFields($include_required);

  /**
   * Get the level of completion of this entity.
   *
   * @return NULL|integer
   *   The percentage completion value.
   */
  public function getCompletionPercentage();

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
