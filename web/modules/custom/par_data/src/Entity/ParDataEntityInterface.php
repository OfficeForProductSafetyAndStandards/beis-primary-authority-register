<?php

namespace Drupal\par_data\Entity;

/**
 * The interface for PAR entities.
 *
 * @ingroup par_data
 */
interface ParDataEntityInterface {

  /**
   * Get the view builder for the entity.
   *
   * @return \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  public function getViewBuilder();

  /**
   * Return the value of the status field.
   *
   * @return NULL|mixed
   *   The value of the status field.
   */
  public function getParStatus();

  /**
   * Get the fields required to complete this entity.
   *
   * @param boolean $include_required
   *   Whether to automatically include required fields.
   *
   * @return NULL|mixed[]
   *   An array of field names.
   */
  public function getCompletionFields($include_required);

  /**
   * Get the level of completion of this entity.
   *
   * @return NULL|integer
   *   The percentage completion value.
   */
  public function getCompletionPercentage();

}
