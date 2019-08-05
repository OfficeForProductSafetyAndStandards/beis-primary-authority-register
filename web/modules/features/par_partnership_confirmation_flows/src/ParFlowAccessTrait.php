<?php

namespace Drupal\par_partnership_confirmation_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    $allowed_statuses = [
      $par_data_partnership->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
      'active',
    ];

    // If this enforcement notice has not been reviewed.
    if (!in_array($par_data_partnership->getRawStatus(), $allowed_statuses)) {
      $this->accessResult = AccessResult::forbidden('This partnership has not been fully reviewed yet.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
