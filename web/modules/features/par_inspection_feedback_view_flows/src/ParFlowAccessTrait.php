<?php

namespace Drupal\par_inspection_feedback_view_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataInspectionFeedback $par_data_inspection_feedback = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_inspection_feedback, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
