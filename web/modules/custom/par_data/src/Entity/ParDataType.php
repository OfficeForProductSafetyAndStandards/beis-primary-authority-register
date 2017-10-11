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
   * Whether the entity is deletable.
   */
  protected $isDeletable;

  /**
   * Whether the entity is deletable.
   */
  protected $isRevokable;

  /**
   * Whether the entity is deletable.
   */
  protected $isArchivable;

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
   * Helper to discover whether this entity can be deleted.
   */
  public function isDeletable() {
    return $this->isDeletable;
  }

  /**
   * Helper to discover whether this entity can be deleted.
   */
  public function isRevokable() {
    return $this->isRevokable;
  }

  /**
   * Helper to discover whether this entity can be deleted.
   */
  public function isArchivable() {
    return $this->isArchivable;
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
    // Get the names of any fields needed for completion.
    $completed_fields = $this->getConfigurationElementByType('entity', 'completed_fields');
    $fields = array_diff($completed_fields, $this->excludedFields());
    return $fields ? $fields : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusTransitions($status) {
    // Get the names of any fields required.
    $status_transitions = $this->getConfigurationElementByType('entity', 'status_transitions');
    if (empty($status_transitions)) {
      return [];
    }

    // Return the statuses that are acceptable to transition from.
    // Or all status values if none are specified.
    if (isset($status_transitions[$status])) {
      return $status_transitions[$status];
    }
    else {
      return $this->getAllowedValues($this->getConfigurationElementByType('entity', 'status_field'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function transitionAllowed($from, $to) {
    $transitions = $this->getStatusTransitions($to);

    // Return the true or False based on the returned value from getStatusTransitions
    // this can either be a string or an array of default options.
    if (isset($transitions[$from]) || isset($transitions)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    // Get the names of any fields required.
    $required_fields = $this->getConfigurationElementByType('entity', 'required_fields');
    if (empty($required_fields)) {
      return [];
    }

    $fields = array_diff($required_fields, $this->excludedFields());
    return $fields ? $fields : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getBooleanFieldLabel($field_name, bool $value = FALSE) {
    $boolean_values = $this->getConfigurationElementByType($field_name, 'boolean_values');
    $key = $value ? 'on' : 'off';
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
