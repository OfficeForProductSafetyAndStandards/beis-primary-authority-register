<?php

namespace Drupal\par_user_block_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

trait ParFlowAccessTrait {

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPerson $par_data_person = NULL, User $user = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    if ($par_data_person) {
      $user = $user ?? $par_data_person->getUserAccount();
    }

    if (!$user) {
      $this->accessResult = AccessResult::forbidden('No user account could be found.');
    }

    if ($access_route_negotiator->getFlowName() === 'block_user' && $user->isBlocked()) {
      $this->accessResult = AccessResult::forbidden('This user is already blocked.');
    }

    if ($access_route_negotiator->getFlowName() === 'unblock_user' && $user->isActive()) {
      $this->accessResult = AccessResult::forbidden('This user is already active.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
