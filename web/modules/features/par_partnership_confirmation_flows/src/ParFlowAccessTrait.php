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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    // Get the parameters for this route.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $allowed_statuses = [
      $par_data_partnership->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
    ];

    // If this enforcement notice has not been reviewed.
    if (!in_array($par_data_partnership->getRawStatus(), $allowed_statuses)) {
      $this->accessResult = AccessResult::forbidden('This partnership has not been fully reviewed yet.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
