<?php

namespace Drupal\par_enforcement_raise_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * {@inheritdoc}
 */
trait ParFlowAccessTrait {

  /**
   * Implements accessCallback().
   *
   * The route, the route match object to be checked and the account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    // Helpdesk and admin users shouldn't be able to complete this flow,
    // new screens need to be added for these users to select the user
    // and authority they're acting on behalf of.
    if ($account->hasPermission('access helpdesk')) {
      $this->accessResult = AccessResult::forbidden('Admin users cannot submit Enforcement notifications.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
