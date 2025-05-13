<?php

namespace Drupal\par_user_role_flows\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\Routing\Route;

/**
 * Checks access for adding institutions to a user.
 */
class RoleChangeCheck implements AccessInterface {

  /**
   * CustomAccessCheck constructor.
   *
   * @param \Drupal\par_roles\ParRoleManagerInterface $parRoleManager
   *   Role Manager Service.
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $flowNegotiator
   *   Flow Negotiator Service.
   */
  public function __construct(
    /**
     * The PAR Role Manager.
     */
    private readonly ParRoleManagerInterface $parRoleManager,
    /**
     * The PAR Flow Negotiator.
     */
    private readonly ParFlowNegotiatorInterface $flowNegotiator,
  ) {
  }

  /**
   * Get the Par Flow Negotiator.
   *
   * @return \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return $this->flowNegotiator;
  }

  /**
   * Get the Par Data Manager.
   *
   * @return \Drupal\par_roles\ParRoleManagerInterface
   */
  public function getParRoleManager(): ParRoleManagerInterface {
    return $this->parRoleManager;
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, UserInterface $user) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException) {

    }

    $current_user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    // Disable role management for blocked users.
    if (!$user->isActive()) {
      return AccessResult::forbidden('This user is already must be active to change their roles.');
    }

    // All allowed roles.
    $roles = [];
    foreach ($this->getParRoleManager()->getAllRoles() as $role) {
      // If the user doesn't have permission ignore the role.
      if (!$current_user || !$role || !$current_user->hasPermission("assign {$role} role")) {
        continue;
      }

      // If the role is an institution role.
      if (in_array($role, $this->getParRoleManager()->getAllInstitutionRoles())) {
        // Get the institution type from the role.
        $institution_type = $this->getParRoleManager()->getInstitutionTypeByRole($role);

        // Only process this role if the user has memberships to this institution type.
        if ($this->getParRoleManager()->hasInstitutions($user, $institution_type)) {
          $roles[] = $role;
        }
      }
      // If the role is a general role.
      else {
        $roles[] = $role;
      }
    }

    // Disable role management for if the current user cannot assign any roles.
    if (empty($roles)) {
      return AccessResult::forbidden('You do not have permission to assign any roles to this user.');
    }

    return AccessResult::allowed();
  }

}
