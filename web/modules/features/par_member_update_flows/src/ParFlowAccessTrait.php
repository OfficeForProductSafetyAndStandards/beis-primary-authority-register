<?php

namespace Drupal\par_member_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParFlowException;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
      $this->getFlowDataHandler()->setParameter('par_data_coordinated_business', $par_data_coordinated_business);
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    // Get the parameters for this route.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');

    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      $this->accessResult = AccessResult::forbidden('This is not a coordinated partnership.');
    }

    // If the membership has been ceased we won't let them re-edit.
    if ($par_data_coordinated_business->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('This member has been ceased you cannot change their details.');
    }

    $locked = FALSE;

    // If the member upload is in progress the member list cannot be modified.
    if ($locked) {
      $this->accessResult = AccessResult::forbidden('This member list is locked because an upload is in progress.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
