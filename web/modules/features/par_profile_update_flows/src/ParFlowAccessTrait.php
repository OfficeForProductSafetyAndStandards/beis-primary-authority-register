<?php

namespace Drupal\par_profile_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Trait for PAR Flow Access.
 */
trait ParFlowAccessTrait {

  /**
   * Implements AccessCallback().
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, User $user = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
