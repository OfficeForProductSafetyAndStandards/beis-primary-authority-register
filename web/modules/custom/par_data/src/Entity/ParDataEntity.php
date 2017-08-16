<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\trance\Trance;

/**
 * Defines the PAR entities.
 *
 * @ingroup par_data
 */
class ParDataEntity extends Trance implements ParDataEntityInterface {

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
   * Get all the relationships for this entity.
   *
   * @param string $target
   *   The target type to return entities for.
   *
   * @return EntityInterface[]
   *   An array of entities keyed by type.
   */
  public function getRelationships($target = NULL) {
    $entities = [];

    // Get all referenced entities.
    $references = $this->getParDataManager()->getReferences($this->getEntityTypeId(), $this->bundle());
    foreach ($references as $entity_type => $fields) {
      // If the reference is on the current entity type
      // we can get the value from the current $entity.
      if ($this->getEntityTypeId() === $entity_type) {
        foreach ($fields as $field_name => $field) {
          foreach ($this->get($field_name)->referencedEntities() as $referenced_entity) {
            $entities[$referenced_entity->getEntityTypeId()][$referenced_entity->id()] = $referenced_entity;
          }
        }
      }
      // If the reference is on another entity type
      // we must use an entity lookup to find all entities
      // that reference the current entity.
      else {
        foreach ($fields as $field_name => $field) {
          $referencing_entities = $this->getParDataManager()->getEntitiesByProperty($entity_type, $field_name, $this->id());
          if ($referencing_entities) {
            if (!isset($entities[$entity_type])) {
              $entities[$entity_type] = [];
            }
            $entities[$entity_type] += $referencing_entities;
          }
        }
      }
    }

    return $target && isset($entities[$target]) ? array_filter($entities[$target]) : $entities;
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
