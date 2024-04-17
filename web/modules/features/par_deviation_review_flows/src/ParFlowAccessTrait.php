<?php

namespace Drupal\par_deviation_review_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Trait for Par Flow Access.
 */
trait ParFlowAccessTrait {

  /**
   * Implements accessCallback().
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataDeviationRequest $par_data_deviation_request = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    // Step 1 & 2 shouldn't be accessed if the deviation request is approved.
    if ($par_data_deviation_request && !$par_data_deviation_request->inProgress() && $access_route_negotiator->getFlow()->getCurrentStep() <= 1) {
      // Set an error if this action has already been reviewed.
      $this->accessResult = AccessResult::forbidden('This action has already been reviewed.');
    }

    // If user is not in the Authority they should not review an enforcement.
    $primary_authority = $par_data_deviation_request ? $par_data_deviation_request->getPrimaryAuthority(TRUE) : NULL;
    $user = User::load($account->id());
    if ($par_data_deviation_request && $primary_authority && !$this->getParDataManager()->isMember($primary_authority, $user)) {
      // Set an error if the user is not in the primary authority.
      $this->accessResult = AccessResult::forbidden('This user is not in the primary authority.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
