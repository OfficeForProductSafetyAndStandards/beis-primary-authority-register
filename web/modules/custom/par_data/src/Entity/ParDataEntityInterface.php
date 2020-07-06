<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\TranceInterface;

/**
 * The interface for PAR entities.
 *
 * @ingroup par_data
 */
interface ParDataEntityInterface extends TranceInterface {

  /**
   * Get the view builder for the entity.
   *
   * @return \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  public function getViewBuilder();

  /**
   * Return the stored value of the status field.
   *
   * @return NULL|mixed
   *   The value of the status field.
   */
  public function getRawStatus();

  /**
   * Return the label of the status field.
   *
   * @return NULL|mixed
   *   The value of the status field.
   */
  public function getParStatus();

  /**
   * Set the status field value.
   *
   * @parap mixed
   *   The value of the status to set.
   */
  public function setParStatus($value);

  /**
   * Any entity that is in a live, active state.
   *
   * Only finished and completed entities count, this is typically useful for
   * entities that go through an approval process.
   *
   * @see Issue PAR-1402
   *
   * @return bool
   *   TRUE if entity is active.
   */
  public function isActive();

  /**
   * Any entity that is in progress can't be revoked, archived or deleted.
   *
   * @return bool
   *   TRUE if entity is in progress.
   */
  public function inProgress();

  /**
   * Get the level of completion of this entity.
   *
   * @return NULL|integer
   *   The percentage completion value.
   */
  public function getCompletionPercentage();

}
