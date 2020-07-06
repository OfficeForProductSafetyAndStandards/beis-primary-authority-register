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
   * @return UserInterface
   */
  public function getUserAccount();

  /**
   * Set the user account.
   *
   * @param mixed $account
   *   Drupal user account.
   */
  public function setUserAccount($account);

  /**
   * Get the User account.
   *
   * @param boolean $link_up
   *   Whether or not to link up the accounts if any are found that aren't already linked.
   *
   * @return array
   *   Returns any other PAR Person records if found.
   */
  public function getSimilarPeople($link_up);

  /**
   * Get the User accounts that have the same email as this PAR Person.
   *
   * @return mixed|null
   *   Returns a Drupal User account if found.
   */
  public function lookupUserAccount();

  /**
   * Link up the PAR Person to a Drupal User account.
   *
   * @param UserInterface $account
   *   An optional user account to lookup.
   *
   * @return bool|int
   *   If there was an account to link to, that wasn't already linked to.
   */
  public function linkAccounts(UserInterface $account);

  /**
   * Get's the e-mail address for this person.
   *
   * @return string|NULL
   *   An email address.
   */
  public function getEmail();

}
