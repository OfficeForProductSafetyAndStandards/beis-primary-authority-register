<?php

namespace Drupal\par_roles;

use Drupal\audit_log\EventSubscriber\User;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\user\UserInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Generator;

/**
 * Interface for the Par Role Manager Service.
 */
interface ParRoleManagerInterface {

  /**
   * Get the roles assigned to an account.
   *
   * @param UserInterface $account
   *   The account to get roles for.
   *
   * @return array
   *   An array of role IDs.
   */
  public function getRoles(UserInterface $account): array;

  /**
   * @param UserInterface $account
   *    The account to display roles for.
   *
   * @return array
   *   An array of role labels.
   */
  public function displayRoles(UserInterface $account): array;

  /**
   * Get the people associated with an account.
   *
   * Note: This can only get people that have already been saved.
   *
   * @param UserInterface $account
   *   The account to get people for.
   *
   * @return ParDataPersonInterface[]
   *   An array of people that are linked to this account.
   */
  public function getPeople(UserInterface $account): array;

  /**
   * Get the user account associated with the person.
   *
   * @param ParDataPersonInterface $person
   *   The person to return the account for.
   *
   * @return ?UserInterface
   *   A user account if one is associated with this person.
   */
  public function getAccount(ParDataPersonInterface $person): ?UserInterface;

  /**
   * Get all the institutions this user is a member of.
   *
   * @param UserInterface $account
   *   The account to get institutions for.
   * @param $institution_type
   *    The institution type to filter by, will check all institutions if nor provided.
   *
   * @return Generator & iterable<ParDataMembershipInterface>
   *   An iterable list of institutions.
   */
  public function getInstitutions(UserInterface $account, $institution_type = NULL): Generator;

  /**
   * Whether the user has any institutions of this type.
   *
   * @param UserInterface $account
   *    The account to get institutions for.
   * @param $institution_type
   *   The institution type to filter by, will check all institutions if nor provided.
   *
   * @return bool
   *   True if the user has institutions.
   *   False if there are none.
   */
  public function hasInstitutions(UserInterface $account, $institution_type = NULL): bool;

  /**
   * Auto assign roles.
   *
   * Based on whether the user has any memberships to any institution,
   * and what their position in those institutions looks like.
   *
   * The following rules will apply:
   *   - 1: They will be assigned the default role in each institution
   *     they belong to, unless they already have a role assigned.
   *   - 2: They will have all institution roles removed for institutions
   *     they do not belong to.
   *
   * @param UserInterface $account
   *    The account to add a role to.
   *
   * @return UserInterface
   *    The account after processing.
   */
  public function autoAssignRoles(UserInterface $account): UserInterface;

  /**
   * Whether the account is blockable.
   *
   * If any of the institution roles this account has cannot be removed
   * then the user will be classed as un-blockable.
   *
   * @return bool
   *   True if the user can be blocked.
   *   False if the user must remain active.
   */
  public function blockable($account): bool;

  /**
   * Add a role.
   *
   * @param UserInterface $account
   *   The account to add a role to.
   * @param string $role
   *   The role id to add.
   *
   * @throws ParRoleException
   *   If the role could not be added.
   *
   * @return UserInterface
   *   The account after processing.
   */
  public function addRole(UserInterface $account, string $role): UserInterface;

  /**
   * Remove a role.
   *
   * @param UserInterface $account
   *   The account to remove a role from.
   * @param string $role
   *   The role id to remove.
   *
   * @throws ParRoleException
   *   If the role could not be removed.
   *
   * @return UserInterface
   *   The account after processing.
   */
  public function removeRole(UserInterface $account, string $role): UserInterface;

  /**
   * Check whether the current user has is allowed to remove the role.
   *
   * @param string $role
   *    The role id to remove.
   *
   * @return bool
   *    True if the user can remove the role.
   *    False if they do not have permission.
   */
  public function canManageRole(string $role): bool;

  /**
   * Add a person to an institution.
   *
   * Note: The institution must be saved after adding members, and the user account
   * belonging to the member must also be saved to automatically re-assign roles.
   *
   * To automatically save the institution and update the user account,
   * @see self::removeUserMemberships();
   *
   * @param ParDataMembershipInterface $institution
   *   An institution to add the member to.
   * @param ParDataPersonInterface $member
   *   The person to remove from the institutions.
   *
   * @return ParDataMembershipInterface
   *   The institution with the member added.
   */
  public function addMember(ParDataMembershipInterface $institution, ParDataPersonInterface $member): ParDataMembershipInterface;

  /**
   * Remove a person from an institution.
   *
   * Note: The institution must be saved after removing members, and the user account
   * belonging to the member must also be saved to automatically re-assign roles.
   *
   * To remove a user entirely from
   * @see self::removeUserMemberships();
   *
   * @param ParDataMembershipInterface $institution
   *   An institution to remove the member from.
   * @param ParDataPersonInterface $member
   *   The person to remove from the institutions.
   *
   * @return ParDataMembershipInterface
   *   The institution with the member removed.
   */
  public function removeMember(ParDataMembershipInterface $institution, ParDataPersonInterface $member): ParDataMembershipInterface;

  /**
   * Remove the user from an institution.
   *
   * Note: The institution must be saved after removing members, and the user account
   * belonging to the member must also be saved to automatically re-assign roles.
   *
   * @param ParDataMembershipInterface $institution
   *   An institution to add the member to or remove them from.
   * @param UserInterface $account
   *   The person to remove from the institution.
   *
   * @return ParDataMembershipInterface
   *   An institution the user was removed from.
   *
   * This is useful in situations where a user needs to be completely removed
   * from an institution (an authority or organisation).
   * @example <caption>Remove user from an institution</caption>
   * $institution = $this->removeUserMemberships($institution, $account);
   * $institution->save();
   * $account->save();
   *
   */
  public function removeUserMembership(ParDataMembershipInterface $institution, UserInterface $account): ParDataMembershipInterface;

}
