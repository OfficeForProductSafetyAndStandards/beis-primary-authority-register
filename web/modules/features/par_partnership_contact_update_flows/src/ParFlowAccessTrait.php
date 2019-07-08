<?php

namespace Drupal\par_partnership_contact_update_flows;

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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, $type = NULL, ParDataPerson $par_data_person = NULL) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
      $this->getFlowDataHandler()->setParameter('type', $type);
      $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    switch ($type) {
      case 'authority':
        if (!$account->hasPermission('update partnership authority contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to update authority contacts.');
        }

        break;

      case 'organisation':
        if (!$account->hasPermission('update partnership organisation contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to update organisation contacts.');
        }

        break;

      default:
        $this->accessResult = AccessResult::forbidden('A valid contact type must be choosen.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
