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
   *
   * @var base
   */
  protected $base;

  /**
   * The entity that the base has a relationship with.
   *
   * @var entity
   */
  protected $entity;

  /**
   * The field definition that creates the relationship.
   *
   * @var field
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
   */
  public function getBaseEntity() {
    return $this->base;
  }

  /**
   * Get the related entity.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Get ID.
   */
  public function getId() {
    return $this->getEntity()->getEntityTypeId() . ':' . $this->getField()->getName();
  }

  /**
   * Get the field that defines the relationship.
   */
  public function getField() {
    return $this->field;
  }

  /**
   * Get Relationship Direction.
   */
  public function getRelationshipDirection() {
    return $this->getField()->getTargetEntityTypeId() === $this->getEntity()->getEntityTypeId() ? self::DIRECTION_REVERSE : self::DIRECTION_DEFAULT;
  }

  /**
   * Sort by.
   */
  public function sortByType($a, $b) {
    return $a->getEntityTypeId();
  }

}
