<?php

namespace Drupal\par_invite_user_flows;

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
   * @param \Drupal\par_data\Entity\ParDataPerson $par_data_person
   *   The person being invited.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPerson $par_data_person = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    // There must be a person, and this person can't have an associated user account.
    if (!$par_data_person || $par_data_person->getUserAccount()) {
      $this->accessResult = AccessResult::forbidden('This person already has an associated user account.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
