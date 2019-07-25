<?php

namespace Drupal\par_partnership_contact_add_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, $type = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    switch ($type) {
      case 'authority':
        if (!$account->hasPermission('add partnership authority contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to update authority contacts.');
        }

        break;

      case 'organisation':
        if (!$account->hasPermission('add partnership organisation contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to update organisation contacts.');
        }

        break;

      default:
        $this->accessResult = AccessResult::forbidden('A valid contact type must be choosen.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
