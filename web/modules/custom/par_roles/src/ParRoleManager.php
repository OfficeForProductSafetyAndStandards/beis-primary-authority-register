<?php

namespace Drupal\par_roles;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;
use Generator;

/**
 * Manages all user role functionality within PAR.
 *
 * Roles within an institution are applied automatically,
 * @see self::autoAssignRoles();
 *
 * Roles can also be added and removed independently,
 * @see self::addRole();
 * @see self::removeRole();
 *
 * Check whether all of a user's roles can be removed and a user account blocked,
 * @see self::blockable();
 *
 * @throw ParRoleException if the action cannot be completed, except self::autoAssignRoles();
 */
class ParRoleManager implements ParRoleManagerInterface {

  /**
   * These roles apply outside any institution memberships, and can
   * be applied to any user by someone with the appropriate permissions.
   */
  const GENERAL_ROLES = [
    'secretary_state', 'senior_administration_officer', 'par_helpdesk', 'national_regulator'
  ];

  /**
   * These roles can only be applied if the user is a member of an institution.
   *
   * This list is hierarchical, the role with the most authority is listed first.
   * This is relevant because there must be another user of similar of higher
   * authority to be able to remove the person from that institution.
   */
  const INSTITUTION_ROLES = [
    'par_data_authority' => ['par_authority_manager', 'par_authority', 'par_enforcement'],
    'par_data_organisation' => ['par_organisation_manager', 'par_organisation']
  ];

  /**
   * The default roles to assign if no role is assigned.
   */
  const DEFAULT_ROLES = [
    'par_data_authority' => 'par_authority',
    'par_data_organisation' => 'par_organisation',
  ];

  /**
   * The label for each type of institution.
   */
  const INSTITUTION_LABEL = [
    'par_data_authority' => 'authority',
    'par_data_organisation' => 'organisation'
  ];

  /**
   * The EntityTypeManager service.
   *
   * @var EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The current user.
   *
   * @var AccountProxyInterface
   */
  protected AccountProxyInterface $currentUser;

  /**
   * Constructs a ParRoleManager instance.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param AccountProxyInterface $current_user
   *   The current user
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * Getter for the EntityTypeManager Service.
   *
   * @return EntityTypeManagerInterface
   *   The entity type manager.
   */
  protected function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->entityTypeManager;
  }

  /**
   * Getter for the user account.
   *
   * @return AccountInterface
   *   A user account.
   */
  protected function getCurrentUser(): AccountInterface {
    return $this->currentUser->getAccount();
  }

  /**
   * Get the label for the institution type.
   */

  /**
   * {@inheritdoc}
   */
  public function getRoles(UserInterface $account): array {
    return $account->getRoles();
  }

  /**
   * {@inheritDoc}
   */
  public function displayRoles(UserInterface $account): array {
    $roles = Role::loadMultiple($account->getRoles(TRUE));
    $labels = [];

    foreach ($roles as $user_role) {
      $labels[] = $user_role->label();
    }

    return $labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getPeople(UserInterface $account): array {
    // Cache this function per request.
    $function_id = __FUNCTION__ . $account->get('mail')->getString();
    $entities = &drupal_static($function_id);
    if (!empty($entities)) {
      return (array) $entities;
    }

    // Add the basic query conditions.
    $query = $this->getPeopleQuery($account);

    // Execute the query.
    $results = $query->execute();
    return $this->getEntityTypeManager()->getStorage('par_data_person')
      ->loadMultiple(array_unique($results));
  }

  /**
   * {@inheritdoc}
   */
  public function hasPeople(UserInterface $account): bool {
    // Add the basic query conditions.
    $query = $this->getPeopleQuery($account);

    return $query->count()->execute() >= 1;
  }

  /**
   * {@inheritDoc}
   */
  public function getAccount(ParDataPersonInterface $person): ?UserInterface {
    return $person->lookupUserAccount();
  }

  /**
   * {@inheritdoc}
   */
  public function getInstitutions(UserInterface $account, $institution_type = NULL): Generator {
    foreach ($this->getPeople($account) as $person) {
      foreach ($person->getInstitutions() as $institution) {
        // Allow institutions to be filtered by type.
        if ($institution_type && $institution->getEntityTypeId() !== $institution_type) {
          continue;
        }

        yield $institution;
      }
    }
  }

  /**
   * Whether the user has any institutions of this type.
   *
   * @param \Drupal\user\UserInterface $account
   * @param $institution_type
   *
   * @return bool
   */
  public function hasInstitutions(UserInterface $account, $institution_type = NULL): bool {
    $institutions = $this->getInstitutions($account, $institution_type);
    return (bool) $institutions->current();
  }

  /**
   * {@inheritdoc}
   */
  public function autoAssignRoles(UserInterface $account): UserInterface {
    // Clone the institution roles for manipulation.
    $roles = self::INSTITUTION_ROLES;

    // Check 1: For each of the members institutions assign the default role.
    foreach ($this->getInstitutions($account) as $institution) {
      $institution_roles = $roles[$institution->getEntityTypeId()] ?? [];

      // Do not re-process this institution if it has already been processed.
      if (empty($institution_roles)) {
        continue;
      }
      unset($roles[$institution->getEntityTypeId()]);

      // If there are no roles for this institution type then assign the default.
      if (empty(array_intersect($institution_roles, $account->getRoles()))) {
        try {
          $account = $this->addRole($account, self::DEFAULT_ROLES[$institution->getEntityTypeId()]);
        }
        catch (ParRoleException) {
          continue;
        }
      }
    }

    // Check 2: Attempt to remove any roles the user has that belong to types
    // of institutions that they are no longer a part of.
    $remaining_roles = array_intersect(
      $account->getRoles(),
      array_merge(...array_values($roles))
    );
    foreach ($remaining_roles as $remaining_role) {
      try {
        $account = $this->removeRole($account, $remaining_role);
      }
      catch (ParRoleException) {
        continue;
      }
    }

    return $account;
  }

  /**
   * {@inheritDoc}
   */
  public function blockable($account, $warning = FALSE): bool {
    // Ensure the account is cloned to not affect the actual user account object.
    $user = clone $account;

    $user_roles = array_intersect(
      $account->getRoles(),
      $this->getAllInstitutionRoles()
    );

    // If any roles cannot be removed the user is un-blockable.
    foreach ($user_roles as $role) {
      try {
        $this->roleRemovable($user, $role);
      }
      catch (ParRoleException $e) {
        if ($warning) {
          \Drupal::messenger()->addWarning($e->getMessage());
        }
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function addRole(UserInterface $account, string $role): UserInterface {
    if ($this->validateAccount($account)) {
      // Check that the role can be assigned to this user.
      $this->roleAllowed($account, $role);

      // Remove all other roles the user has for this type of institution.
      $institution_type = $this->getInstitutionTypeByRole($role);
      $institution_roles = self::INSTITUTION_ROLES[$institution_type] ?? [];

      // Get all the roles of this institution type the user has,
      // that aren't the role being added.
      $remove_to_roles = array_diff(
        array_intersect(
          $account->getRoles(),
          $institution_roles
        ),
        [$role]
      );

      foreach ($remove_to_roles as $remove_role) {
        $account = $this->removeRole($account, $remove_role);
      }
    }

    // Add the role after all old roles have been removed to ensure roles aren't doubled up.
    $account->addRole($role);

    return $account;
  }

  /**
   * {@inheritDoc}
   */
  public function removeRole(UserInterface $account, string $role): UserInterface {
    // Check that the role can be removed.
    if ($this->validateAccount($account)) {
      $this->roleRemovable($account, $role);
    }

    // Remove the role.
    $account->removeRole($role);

    return $account;
  }

  /**
   * {@inheritDoc}
   */
  public function canManageRole(string $role): bool {
    // Confirm that the current user has permission to assign the role.
    $permission = sprintf('assign %s role', $role);
    return $this->getCurrentUser()->hasPermission($permission);
  }

  /**
   * {@inheritDoc}
   */
  public function addMember(ParDataMembershipInterface $institution, ParDataPersonInterface $member): ParDataMembershipInterface {
    // Check whether the member can be added.
    if (!$institution->hasPerson($member)) {
      // Ensure the person is saved before they are added.
      if ($member->isNew()) {
        $member->save();
      }

      // Add the member.
      $institution->addPerson($member);
    }

    return $institution;
  }

  /**
   * {@inheritDoc}
   */
  public function removeMember(ParDataMembershipInterface $institution, ParDataPersonInterface $member): ParDataMembershipInterface {
    // Check whether the member can be removed.
    if ($institution->hasPerson($member)) {
      // Add the member.
      $institution->removePerson($member);
    }

    return $institution;
  }

  /**
   * {@inheritDoc}
   */
  public function removeUserMembership(ParDataMembershipInterface $institution, UserInterface $account): ParDataMembershipInterface {
    foreach ($this->getPeople($account) as $person) {
      // Confirm they are a member before removing.
      if ($institution->hasPerson($person)) {
        // Remove the member from the institution.
        $institution = $this->removeMember($institution, $person);
      }
    }

    return $institution;
  }

  /**
   * Gets the query interface needed to look up the people related to a user.
   *
   * @param UserInterface $account
   *   The user account to lookup.
   *
   * @return QueryInterface
   */
  private function getPeopleQuery(UserInterface $account): QueryInterface {
    $query = $this->getEntityTypeManager()->getStorage('par_data_person')
      ->getQuery('OR')
      ->accessCheck(FALSE)
      ->sort('id', 'DESC');


    // Add the basic query conditions.
    $query->condition('email', $account->getEmail());

    // If they have a registered account use the ID also.
    if (!$account->isNew()) {
      $query->condition('field_user_account', $account->id(), 'IN');
    }

    return $query;
  }

  /**
   * Get the label for a given role.
   *
   * @param string $role
   *   The role label to get.
   *
   * @return string
   *   The label.
   */
  protected function getRoleLabel(string $role): string {
    return (string) Role::load($role)?->label();
  }

  /**
   * Whether the role can be added.
   *
   * The following rules are used to determine if the role can be added:
   *   - The user must be in an institution that supports that role (each
   *     institution role belongs to one type of institution only).
   */
  protected function roleAllowed(UserInterface $account, string $role): bool {
    // Check 1: Institution Roles
    // These can only be assigned if the user belongs to an institution of that type.
    foreach (self::INSTITUTION_ROLES as $institution_type => $institution_roles) {
      // If the role isn't an institution role then no checks need to be performed.
      if (!in_array($role, $institution_roles, TRUE)) {
        continue;
      }

      // Check that there is at least one institution.
      if (!$this->hasInstitutions($account, $institution_type)) {
        throw new ParRoleException(
          sprintf(
            "Cannot add %s because the user is not a member of any %s",
            $role,
            self::INSTITUTION_LABEL[$institution_type],
          )
        );
      }
    }

    // Last Check: Ensure all roles are valid.
    return in_array($role, $this->getAllRoles());
  }

  /**
   * Whether the role can be removed.
   *
   * The following rules determine whether a role can be removed:
   *   - There must be at least one other colleague in each of their institutions.
   *     An institution must have at least one member of a management level role.
   */
  protected function roleRemovable(UserInterface $account, string $role): bool {
    // Check 1: Institution Roles
    // These can only be removed if there is at least one other user in that institution.
    foreach (self::INSTITUTION_ROLES as $institution_type => $institution_roles) {
      // If the role isn't an institution role then no checks need to be performed.
      if (!in_array($role, $institution_roles, TRUE)) {
        continue;
      }

      // Check that there is at least one other user in each of these institutions.
      foreach ($this->getInstitutions($account, $institution_type) as $institution) {
        if ($this->isLastMember($institution, $account, $role)) {
          throw new ParRoleException(
            sprintf("Cannot remove the %s role because there are no other members with this role in the %s %s.",
              $this->getRoleLabel($role),
              self::INSTITUTION_LABEL[$institution_type],
              $institution->label(),
            )
          );
        }
      }
    }

    // By default, any roles that don't have additional checks are permitted for the user.
    return TRUE;
  }

  /**
   * Get the type of institution based on the role.
   *
   * @param string $role
   *   The role to search for.
   *
   * @return ?string
   *   The institution type.
   */
  public function getInstitutionTypeByRole(string $role): ?string {
    // Lookup the institution type for this role.
    foreach (self::INSTITUTION_ROLES as $institution_type => $institution_roles) {
      if (in_array($role, $institution_roles)) {
        return $institution_type;
      }
    }

    return NULL;
  }

  /**
   * Get all the roles available in PAR.
   *
   * @return array
   *   An array of roles.
   */
  public function getAllRoles(): array {
    // Combine all institution roles and general roles.
    return array_merge(self::GENERAL_ROLES, $this->getAllInstitutionRoles());
  }

  /**
   * Get all the institution roles.
   *
   * @return array
   *   An array of roles.
   */
  public function getAllInstitutionRoles(): array {
    // Flatten self::INSTITUTION_ROLES into a one dimensional array.
    return array_merge(...array_values(self::INSTITUTION_ROLES));
  }

  /**
   * Get all the roles of similar or higher authority in the role hierarchy.
   *
   * @param string $role
   *    The role to start the hierarchy search for.
   * @param ?string $institution_type
   *   The institution type to check.
   *
   * @return array|string[]
   */
  public function getRolesByHierarchy(string $role, string $institution_type = NULL): array {
    $roles = $institution_type ?
      self::INSTITUTION_ROLES[$institution_type] : self::GENERAL_ROLES;

    $index = array_search($role, $roles);
    $position = $index !== FALSE ? $index + 1 : NULL;

    return $position ?
      array_slice($roles, 0, $position) : [];
  }

  /**
   * Get all the user's colleagues in a given institution.
   *
   * @param ParDataMembershipInterface $institution
   *   The institution to check.
   * @param UserInterface $account
   *   The user to exclude.
   *
   * @return UserInterface[]
   *   All user accounts that are colleagues.
   */
  protected function getColleagues(ParDataMembershipInterface $institution, UserInterface $account): array {
    return array_filter($institution->getMembers(), function($member) use ($account) {
      return $member->id() !== $account->id();
    });
  }

  /**
   * Whether this user is the last member in the institution.
   *
   * By passing in the optional $roles parameter the role hierarchy in the
   * institution will be used to only look for colleagues with a similar or
   * higher role.
   *
   * @param ParDataMembershipInterface $institution
   *   The institution to check for.
   * @param UserInterface $account
   *   The user to check.
   * @param ?string $role
   *   The role to check, if none is supplied all roles will be checked.
   *
   * @return bool
   *   Whether there are any other users in the institution.
   */
  protected function isLastMember(ParDataMembershipInterface $institution, UserInterface $account, string $role = NULL): bool {
    // Get all the roles of similar or great authority in the role hierarchy.
    $required_roles = $role ?
      $this->getRolesByHierarchy($role, $institution->getEntityTypeId()) :
      self::INSTITUTION_ROLES[$institution->getEntityTypeId()];

    // Check each colleague to see if they have any of the required roles.
    foreach ($this->getColleagues($institution, $account) as $colleague) {
      if (!empty(array_intersect($required_roles, $colleague->getRoles()))) {
        return FALSE;
      }
    }

    // If no colleagues were found then this is the last member.
    return TRUE;
  }

  /**
   * Whether to validate an account.
   *
   * This enables roles checks to be bypassed in some situations:
   *   - For the admin role.
   *   - For test accounts that end with @exmaple.com
   *
   * @param UserInterface $account
   *   The account to validate.
   *
   * @return bool
   */
  private function validateAccount(UserInterface $account): bool {
    // Do not run validation checks for user #1.
    if ($account->isAnonymous() || $account->id() === 1) {
      return FALSE;
    }

    // Do not run validation checks for test accounts.
    if (preg_match('/@example.com$/', $account->getEmail()) === 1) {
      return FALSE;
    }

    return TRUE;
  }

}
