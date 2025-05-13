<?php

namespace Drupal\par_data\Entity;

use Drupal\user\UserInterface;

/**
 * The interface for PAR person entities.
 *
 * @ingroup par_data
 */
interface ParDataPersonInterface extends ParDataEntityInterface {

  /**
   * Get the User account.
   *
   * @return ?UserInterface
   */
  public function getUserAccount(): ?UserInterface;

  /**
   * Set the user account.
   *
   * @param mixed $account
   *   The Drupal user account.
   */
  public function setUserAccount(mixed $account);

  /**
   * Get the User account.
   *
   * @param bool $link_up
   *   Whether to link up the accounts if any are found that aren't already linked.
   *
   * @return ParDataPersonInterface[]
   *   Returns any other PAR Person records if found.
   */
  public function getSimilarPeople(bool $link_up): array;

  /**
   * Get the User accounts that have the same email as this PAR Person.
   *
   * @return ?UserInterface
   *   Returns a Drupal User account if found.
   */
  public function lookupUserAccount(): ?UserInterface;

  /**
   * Link up the PAR Person to a Drupal User account.
   *
   * @param \Drupal\user\UserInterface $account
   *   An optional user account to lookup.
   *
   * @return ?UserInterface
   *   If there was an account to link to, that wasn't already linked to.
   */
  public function linkAccounts(UserInterface $account): ?UserInterface;

  /**
   * Gets the e-mail address for this person.
   *
   * @return ?string
   *   An email address.
   */
  public function getEmail(): ?string;

  /**
   * Get PAR Person's full name.
   *
   * @return string
   *   Their full name including title/salutation field.
   */
  public function getFullName(): string;

  /**
   * Get the institutions related to this person.
   *
   * An institution is an entity that a person can belong to and be a member of.
   *
   * @param ?string $type
   *   The institution type to return, defaults to returning all institutions.
   *
   * @return \Generator & iterable<ParDataMembershipInterface>
   *   The institutions belonging to this person.
   */
  public function getInstitutions(?string $type = NULL): iterable;

}
