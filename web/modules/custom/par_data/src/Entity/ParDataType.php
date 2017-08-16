<?php

namespace Drupal\par_data\Entity;

use Drupal\par_data\ParDataManagerInterface;
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
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

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
    // Get the names of any fields required for completion.
    $required_fields = $this->getConfigurationElementByType('entity', 'required_fields');
    $fields = array_diff($required_fields, $this->excludedFields());
    return $fields ? $fields : [];
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
    return $allowed_values ? $allowed_values : [];
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
