<?php

namespace Drupal\par_person_merge_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPerson $par_data_person = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    $people = $par_data_person ? $par_data_person->getSimilarPeople() : NULL;
    if (!$people || count($people) <= 1) {
      $this->accessResult = AccessResult::forbidden('There are not enough matching contact records to merge.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
