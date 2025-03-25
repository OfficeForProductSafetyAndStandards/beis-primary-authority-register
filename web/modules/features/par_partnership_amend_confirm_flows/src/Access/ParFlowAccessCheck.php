<?php

namespace Drupal\par_partnership_amend_confirm_flows\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
* Checks access for displaying the amend partnership pages.
*/
class ParFlowAccessCheck implements AccessInterface {

  /**
   * CustomAccessCheck constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $parDataManager
   *   Data Manager Service
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $flowNegotiator
   *   Flow Negotiator Service
   */
  public function __construct(
      /**
       * The PAR Data Manager.
       */
      private readonly ParDataManagerInterface $parDataManager,
      /**
       * The PAR Flow Negotiator.
       */
      private readonly ParFlowNegotiatorInterface $flowNegotiator
  )
  {
  }

  /**
   * Get the Par Flow Negotiator.
   *
   * @return \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return $this->flowNegotiator;
  }

  /**
   * Get the Par Data Manager.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  public function getParDataManager(): ParDataManagerInterface {
    return $this->parDataManager;
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException) {

    }

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') &&
      (!$user || !$this->getParDataManager()->isMember($par_data_partnership, $user))) {
      return AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Only active partnerships can be amended.
    if (!$par_data_partnership->isActive()) {
      return AccessResult::forbidden('Only active partnerships can be amended.');
    }

    $partnership_legal_entities = $par_data_partnership->getPartnershipLegalEntities();
    // Get only the partnership legal entities that are awaiting confirmation.
    $partnership_legal_entities = array_filter($partnership_legal_entities, fn($partnership_legal_entity) => $partnership_legal_entity->getRawStatus() === 'confirmed_authority');

    // Can only be confirmed if there are some entities to confirm.
    if (empty($partnership_legal_entities)) {
      return AccessResult::forbidden('Only partnerships with pending legal entity amendments can be confirmed.');
    }

    return AccessResult::allowed();
  }

}
