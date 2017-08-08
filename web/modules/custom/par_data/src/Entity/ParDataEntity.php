<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\trance\Trance;

/**
 * Defines the PAR entities.
 *
 * @ingroup par_data
 */
class ParDataEntity extends Trance implements ParDataEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    $label_fields = $this->type->entity->getConfigurationByType('entity', 'label_fields');
    if (isset($label_fields) && is_string($label_fields)) {

    }
    else if (isset($label_fields) && is_array($label_fields)) {
      $label = '';
      foreach ($label_fields as $field) {
        if ($this->hasField($field)) {
          $label .= $this->get($field)->getString();
        }
      }
    }

    return isset($label) && !empty($label) ? $label : parent::label();
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
    $field_name = $this->type->entity->getConfigurationByType('entity', 'status_field');

    if (isset($field_name) && $this->hasField($field_name)) {
      $status = $this->get($field_name)->getString();
    }

    return isset($status) ? $status : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionPercentage($include_deltas = FALSE) {
    $total = 0;
    $completed = 0;

    $fields = $this->getCompletionFields();
    foreach ($fields as $field_name) {
      if ($include_deltas) {
        // @TODO Count multiple field values individually rather than as one field.
      }
      else {
        if ($this->hasField($field_name)) {
          ++$total;
          if (!$this->get($field_name)->isEmpty()) {
            ++$completed;
          }
        }
      }
    }

    return $total > 0 ? ($completed / $total) * 100 : 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionFields($include_required = FALSE) {
    $fields = [];

    // Get the names of any extra fields required for completion.
    $required_fields = $this->type->entity->getConfigurationByType('entity', 'required_fields');

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
   * Get boolean fields for this entity.
   *
   * @return array
   *   An array of field names.
   */
  public function getBooleanFields() {

  }

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
  public function getBooleanFieldLabel($field_name, bool $value = FALSE) {
    $boolean_values = $this->type->entity->getConfigurationByType($field_name, 'boolean_values');
    $key = $value ? 1 : 0;
    return $this->hasField($field_name) && isset($boolean_values[$key]) ? $boolean_values[$key] : FALSE;
  }

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
  public function getAllowedFieldlabel($field_name, $value = FALSE) {
    $allowed_values = $this->type->entity->getConfigurationByType($field_name, 'allowed_values');
    return $this->hasField($field_name) && isset($boolean_values[$key]) ? $boolean_values[$key] : FALSE;
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

  /**
   * Set a default administrative title for entities where we don't really need one.
   *
   * @return string
   */
  public static function setDefaultTitle() {
    return uniqid();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = $fields['name']->setDefaultValueCallback(__CLASS__ . '::setDefaultTitle')
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ]);

    return $fields;
  }

}
