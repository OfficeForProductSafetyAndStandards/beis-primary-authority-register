<?php

namespace Drupal\par_data\Entity;

/**
 * The interface for PAR entities.
 *
 * @ingroup par_data
 */
interface ParDataEntityInterface {

  /**
   * A method to get all the member authorities and organisations
   * for this entity.
   *
   * @param string $action
   *   The action being performed on an entity.
   * @param $account
   *   An optional account object to be used to determine membership by role.
   * @param array $members
   *   An optional array to append to.
   *
   * @return array|false
   *   An array of member entities keyed by uuid
   *   or false if permissions don't require membership.
   */
  public function getMembers($action, $account, $members);

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
   * Get the level of completion of this entity.
   *
   * @return NULL|integer
   *   The percentage completion value.
   */
  public function getCompletionPercentage();

}
