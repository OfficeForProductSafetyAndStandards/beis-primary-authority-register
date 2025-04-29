<?php

namespace Drupal\par_person_membership_flows\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\Routing\Route;

/**
* Checks access for removing institutions from a user.
*/
class InstitutionRemovalCheck implements AccessInterface {

  /**
   * CustomAccessCheck constructor.
   *
   * @param ParRoleManagerInterface $parRoleManager
   *   Role Manager Service
   * @param ParFlowNegotiatorInterface $flowNegotiator
   *   Flow Negotiator Service
   */
  public function __construct(
      /**
       * The PAR Role Manager.
       */
      private readonly ParRoleManagerInterface $parRoleManager,
      /**
       * The PAR Flow Negotiator.
       */
      private readonly ParFlowNegotiatorInterface $flowNegotiator
  )
  {
  }

  /**
   * Get the Par Flow Negotiator.
   *
   * @return ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return $this->flowNegotiator;
  }

  /**
   * Get the Par Data Manager.
   *
   * @return ParRoleManagerInterface
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
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, UserInterface $user, $institution_type = NULL, $institutuion_id = NULL) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException) {

    }

    $current_user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    // Disable users from managing their own memberships.
    if ($current_user->isAuthenticated() && $user->id() === $current_user->id()) {
      return AccessResult::forbidden('This user is already must be active to change their roles.');
    }

    return AccessResult::allowed();
  }

}
