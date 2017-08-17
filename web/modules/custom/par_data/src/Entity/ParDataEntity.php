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
   * Validate the fields.
   *
   * @param array $fields
   * 'comments' => [
   *   'value' => $form_state->getValue('about_business'),
   *   'type' => 'boolean',
   *   'min_selected' => 1,
   *   'key' => 'about_business',
   *   'tokens' => [
   *      '%field' => $form['about_business']['#title']->render(),
   *    ]
   *  ],
   *
   * min_selected - IF not specified then all needs to be checked.
   * @return array
   *   Array of errors with field names or empty if there are no errors.
   */
  public function validateFields(array $fields) {
    $error = [];
    $required_fields = $this->getRequiredFields();

    foreach($fields as $field_name => $field_info) {
      if (!empty($required_fields[$field_name])) {

        // Field has been located so need to validate it.
        if (empty($field_info['value'])) {
          $error[$field_info['key']] = t('<a href="#edit-' . str_replace('_', '-', $field_info['key'])  . '">' . $required_fields[$field_name] . '</a>', $field_info['tokens']);
        }
        elseif (is_array($field_info['value'])) {
          // Check the type of hte field if specified.
          if ($field_info['type'] === 'boolean') {

            $count = count($field_info['value']);

            if (isset($field_info['min_selected'])) {
              $count = $field_info['min_selected'];
            }
            foreach ($field_info['value'] as $item => $value) {
              if ($value) {
                $count--;
              }
              if ($count <= 0) {
                break;
              }
            }

            if ($count > 0) {
              $error[$field_info['key']] = t('<a href="#edit-' . str_replace('_', '-', $field_info['key'])  . '">' . $required_fields[$field_name] . '</a>', $field_info['tokens']);
            }
          }
        }
      }
    }

    return $error;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $label_fields = $this->getTypeEntity()->getConfigurationElementByType('entity', 'label_fields');
    if (isset($label_fields) && is_string($label_fields)) {

    }
    else if (isset($label_fields) && is_array($label_fields)) {
      $label = '';
      foreach ($label_fields as $field) {
        if ($this->hasField($field)) {
          $label .= " " . $this->get($field)->getString();
        }
      }
    }

    return isset($label) && !empty($label) ? $label : parent::label();
  }

  /**
   * Get the value for a field.
   *
   * @return string
   */
  public function retrieveStringValue($field_name) {
    $field = $this->hasField($field_name) ? $this->get($field_name) : NULL;
    $value = isset($field) ? $field->getString() : '';
    return $value;
  }

  /**
   * Get the value for a field.
   *
   * @return []
   */
  public function retrieveEntityValue($field_name) {
    $field = $this->hasField($field_name) ? $this->get($field_name) : NULL;
    return isset($field) && $field->getFieldDefinition()->getType() !== 'entity_reference' ? $field->referencedEntities() : [];
  }

  /**
   * Get the value for a field.
   *
   * @return boolean
   */
  public function retrieveBooleanValue($field_name) {
    $field = $this->hasField($field_name) ? $this->get($field_name) : NULL;
    return isset($field) && $field->getFieldDefinition()->getType() !== 'boolean' ? !empty($field->getString()) : FALSE;
  }

  /**
   * Get the type entity.
   */
  public function getTypeEntity() {
    return $this->type->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getViewBuilder() {
    return \Drupal::entityTypeManager()->getViewBuilder($this->getEntityTypeId());
  }

  /**
   * Get all reference fields for a given entity
   */
  public function getReferenceFields() {
    $reference_fields = [];
    foreach ($this->getFieldDefinitions() as $definition) {
      if ($definition->getType() === 'entity_reference'
        && isset($this->get($definition->getName())->referencedEntities()[0])
        && $this->get($definition->getName())->referencedEntities()[0] instanceof ParDataEntityInterface) {
        $reference_fields[$definition->getName()] = $this->get($definition->getName());
      }
    }

    return $reference_fields;
  }

  /**
   * Get all reference fields for a given entity
   */
  public function getReferenceEntitiesByType($type) {
    $entities = [];
    foreach ($this->getFieldDefinitions() as $definition) {
      if ($definition->getsetting('target_type') === $type) {
        foreach ($this->get($definition->getName())->referencedEntities() as $entity) {
          $entities[$entity->id()] = $entity;
        }
      }
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function getRawStatus() {
    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');

    if (isset($field_name) && $this->hasField($field_name)) {
      $status = $this->get($field_name)->getString();
    }

    return isset($status) ? $status : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getParStatus() {
    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $raw_status = $this->getRawStatus();
    return $this->getTypeEntity()->getAllowedFieldlabel($field_name, $raw_status);
  }

  /**
   * {@inheritdoc}
   */
  public function setParStatus($value) {
    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $allowed_values = $this->getTypeEntity()->getAllowedValues($field_name);
    if (isset($allowed_values[$value])) {
      $this->set($field_name, $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return $this->getTypeEntity()->getRequiredFields();
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionPercentage($include_deltas = FALSE) {
    $total = 0;
    $completed = 0;

    $fields = $this->getTypeEntity()->getCompletionFields();
    foreach ($fields as $field_name) {
      if ($include_deltas) {
        // @TODO Count multiple field values individually rather than as one field.
      }
      else {
        if ($this->hasField($field_name)) {
          ++$total;
          if (!$this->get($field_name)->isEmpty() && !empty($this->get($field_name)->getString())) {
            ++$completed;
          }
        }
      }
    }

    return $total > 0 ? ($completed / $total) * 100 : 0;
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
