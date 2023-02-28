<?php

namespace Drupal\par_data\Entity;

use Drupal\user\UserInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;

/**
 * The interface for PAR entities that support membership subscription.
 *
 * @ingroup par_data
 */
interface ParDataMembershipInterface {

  /**
   * Get subscribers to this entity.
   *
   * @return UserInterface[]
   *   An array of users that are subscribed to this entity.
   */
  public function getMembers(): array;

  /**
   * Get all the contacts records associated with this membership.
   *
   * @param bool $primary
   *   Whether to return just the primary contact record.
   *
   * @return ParDataPersonInterface[]
   */
  public function getPerson(bool $primary = FALSE): mixed;
}
