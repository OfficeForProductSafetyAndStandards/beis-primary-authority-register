<?php

namespace Drupal\par_authority_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataAuthority $par_data_authority = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    // The authority name cannot be changed if there are active partnerships or enforcements.
    $entities = $this->getParDataManager()->getRelatedEntities($par_data_authority, [], 1, 'manage');
    $banned_entities = array_filter($entities, function ($entity) {
      // Do not follow relationships from secondary people.
      return (array_search($entity->getEntityTypeId(), ['par_data_partnership']) !== FALSE);
    });

    if ($route_match->getRouteName() === 'par_authority_update_flows.authority_update_name' && !empty($banned_entities)) {
      // Set an error if this action has already been reviewed.
      $this->accessResult = AccessResult::forbidden('This action has already been reviewed.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
