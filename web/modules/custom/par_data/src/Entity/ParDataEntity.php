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
