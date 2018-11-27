<?php

namespace Drupal\par_data;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * A class that defines relationships.
 */
class ParDataRelationship {

  const DIRECTION_DEFAULT = 'default';
  const DIRECTION_REVERSE = 'reverse';

  /**
   * The entity that the relationship is based on.
   */
  protected $base;

  /**
   * The entity that the base has a relationship with.
   */
  protected $entity;

  /**
   * The field definition that creates the relationship.
   */
  protected $field;

  /**
   * Constructs an instance of a PAR data relationship.
   *
   * @param \Drupal\Core\Entity\EntityInterface $base
   *   The base entity.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The related entity.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field
   *   The relationship field.
   */
  public function __construct(EntityInterface $base, EntityInterface $entity, FieldDefinitionInterface $field) {
    $this->base = $base;
    $this->entity = $entity;
    $this->field = $field;
  }

  /**
   * Get the base entity.
   *
   * @return EntityInterface
   */
  public function getBaseEntity() {
    return $this->base;
  }

  /**
   * Get the related entity.
   *
   * @return EntityInterface
   */
  public function getEntity() {
    return $this->entity;
  }

  public function getId() {
    return $this->getEntity()->getEntityTypeId() . ':' . $this->getField()->getName();
  }

  /**
   * Get the field that defines the relationship.
   *
   * @return FieldDefinitionInterface
   */
  public function getField() {
    return $this->field;
  }

  public function getRelationshipDirection() {
    return $this->getField()->getTargetEntityTypeId() === $this->getEntity()->getEntityTypeId() ? self::DIRECTION_REVERSE : self::DIRECTION_DEFAULT;
  }

  public function sortByType($a, $b) {
    return $a->getEntityTypeId();
  }
}
