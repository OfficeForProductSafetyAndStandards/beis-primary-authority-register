<?php

namespace Drupal\par_member_list_update_flows\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Checks access for par flows.
 */
class ParFlowAccessCheck implements AccessInterface {

  /**
   * CustomAccessCheck constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface
   *   Data Manager Service
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface
   *   Flow Negotiator Service
   * @param \Drupal\par_flows\ParFlowDataHandlerInterface
   *   Flow Data Handler Service
   */
  public function __construct(
      /**
       * The PAR Data Manager.
       */
      private readonly ParDataManagerInterface $parDataManager,
      /**
       * The PAR Flow Negotiator.
       */
      private readonly ParFlowNegotiatorInterface $flowNegotiator,
      /**
       * The PAR Flow Negotiator.
       */
      private readonly ParFlowDataHandlerInterface $flowDataHandler
  )
  {
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->flowNegotiator->cloneFlowNegotiator($route_match);
    } catch (ParFlowException) {

    }

    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      return AccessResult::forbidden('This is not a coordinated partnership.');
    }

    // Check the user has permission to manage the current organisation.
    if (!$account->hasPermission('bypass par_data membership')
      && !$this->parDataManager->isMember($par_data_partnership->getOrganisation(TRUE), $user)) {
      return AccessResult::forbidden('User does not have permissions to manage this partnership.');
    }

    return AccessResult::allowed();
  }

}
