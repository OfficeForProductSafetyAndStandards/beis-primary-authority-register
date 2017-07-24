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
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionFields() {
    $fields = [];

    // Get the names of any extra fields required for completion.
    $required_fields = $this->getBundleEntity()->getConfigurationByType('entity', 'required_fields');

    // Get all the required fields on an entity.
    foreach ($this->getFieldDefinitions() as $field_name => $field_definition) {
      if ($field_definition->isRequired()) {
        $fields[] = $field_name;
      }
      elseif (isset($required_fields) && in_array($field_name, $required_fields)) {
        $fields[] = $field_name;
      }
    }

    return $fields;
  }

}
