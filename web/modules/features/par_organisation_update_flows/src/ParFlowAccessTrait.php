<?php

namespace Drupal\par_organisation_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 *
 */
trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataOrganisation $par_data_organisation = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
