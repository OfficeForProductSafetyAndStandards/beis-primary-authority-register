<?php

namespace Drupal\par_member_upload_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
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

    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      $this->accessResult = AccessResult::forbidden('This is not a coordinated partnership.');
    }

    // Check the user has permission to manage the current organisation.
    if (!$user || (!$account->hasPermission('bypass par_data membership')
      && !$this->getParDataManager()->isMember($par_data_partnership->getOrganisation(TRUE), $user))) {
      $this->accessResult = AccessResult::forbidden('User does not have permissions to remove authority contacts from this partnership.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
