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
  public function getConfiguration() {
    return !empty($this->configuration) ? $this->configuration : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationElement($element) {
    $config = $this->getConfiguration();
    return isset($config[$element]) ? $config[$element] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationByType($type) {
    $elements = [];
    foreach ($this->getConfiguration() as $element => $configurations) {
      if ($config = $this->getConfigurationElementByType($element, $type)) {
        $elements[$element] = $config;
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationElementByType($element, $type) {
    $element_configuration = $this->getConfigurationElement($element);
    return isset($element_configuration[$type]) ? $element_configuration[$type] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionFields($include_required = FALSE) {
    $fields = [];

    // Get the names of any extra fields required for completion.
    $required_fields = $this->getConfigurationElementByType('entity', 'required_fields');

    // Get all the required fields on an entity.
    foreach ($this->getFieldDefinitions() as $field_name => $field_definition) {
      if ($include_required && $field_definition->isRequired() && !in_array($field_name, $this->excludedFields())) {
        $fields[] = $field_name;
      }
      elseif (isset($required_fields) && in_array($field_name, $required_fields)) {
        $fields[] = $field_name;
      }
    }

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getBooleanFieldLabel($field_name, bool $value = FALSE) {
    $boolean_values = $this->getConfigurationElementByType($field_name, 'boolean_values');
    $key = !empty($value) ? 'on' : 'off';
    return isset($boolean_values[$key]) ? $boolean_values[$key] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowedValues($field_name) {
    $allowed_values = $this->getConfigurationElementByType($field_name, 'allowed_values');
    return $allowed_values ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowedFieldlabel($field_name, $value = FALSE) {
    $allowed_values = $this->getConfigurationElementByType($field_name, 'allowed_values');
    return isset($allowed_values[$value]) ? $allowed_values[$value] : FALSE;
  }

  /**
   * System fields excluded from user input.
   */
  protected function excludedFields() {
    return [
      'id',
      'type',
      'uuid',
      'user_id',
      'created',
      'changed',
      'name'
    ];
  }

}
