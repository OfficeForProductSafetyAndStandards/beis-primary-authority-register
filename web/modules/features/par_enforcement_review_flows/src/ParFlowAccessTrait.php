<?php

namespace Drupal\par_enforcement_review_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_notice', $par_data_enforcement_notice);
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    // Get the parameters for this route.
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // Steps 1 & 2 shouldn't be accessed if the enforcement notice has already been approved.
    if (!$par_data_enforcement_notice->inProgress() && $this->getFlowNegotiator()->getFlow()->getCurrentStep() <= 2) {
      // Set an error if this action has already been reviewed.
      $this->accessResult = AccessResult::forbidden('This action has already been reviewed.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
