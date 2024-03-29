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

  /**
   * Add a person.
   *
   * @param ParDataPersonInterface $person
   */
  public function addPerson(ParDataPersonInterface $person): void;

  /**
   * Remove a person.
   *
   * @param ParDataPersonInterface $person
   */
  public function removePerson(ParDataPersonInterface $person): void;

  /**
   * Whether the person is already on the authority.
   *
   * @param ParDataPersonInterface $person
   *
   * @return bool
   *   TRUE if the person already exists on the institution.
   *   FALSE if not found on the institution.
   */
  public function hasPerson(ParDataPersonInterface $person): bool;

}
