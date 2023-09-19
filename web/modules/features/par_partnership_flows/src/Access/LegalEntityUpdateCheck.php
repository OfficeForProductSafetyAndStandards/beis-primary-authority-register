<?php

namespace Drupal\par_partnership_flows\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipLegalEntity;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
* Checks access for updating legal entities on the partnership.
*/
class LegalEntityUpdateCheck implements AccessInterface {

  /**
   * The PAR Data Manager.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  private ParDataManagerInterface $parDataManager;

  /**
   * The PAR Flow Negotiator.
   *
   * @var \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  private ParFlowNegotiatorInterface $flowNegotiator;

  /**
   * CustomAccessCheck constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   Data Manager Service
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $flow_negotiator
   *   Flow Negotiator Service
   */
  public function __construct(ParDataManagerInterface $par_data_manager, ParFlowNegotiatorInterface $flow_negotiator) {
    $this->parDataManager = $par_data_manager;
    $this->flowNegotiator = $flow_negotiator;
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
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, ParDataPartnershipLegalEntity $par_data_partnership_le = NULL) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      return AccessResult::forbidden('The user is not allowed to access this page.');
    }

    // Restrict access when partnership is active to users with administrator
    // role and legacy legal entities that need to be updated.
    if ($par_data_partnership->isActive() &&
      (!$account->hasPermission('amend active partnerships') ||
      !$par_data_partnership_le?->getLegalEntity()?->isLegacyEntity())) {
      return AccessResult::forbidden('This partnership is active therefore the legal entities cannot be changed.');
    }

    // Restrict business users who have already confirmed their business details.
    if ($par_data_partnership->getRawStatus() === 'confirmed_business' && !$account->hasPermission('approve partnerships')) {
      return AccessResult::forbidden('This partnership has been confirmed by the business therefore the legal entities cannot be changed.');
    }

    // Only legacy legal entities can be updated.
    if (!$par_data_partnership_le?->getLegalEntity()?->isEditable()) {
      return AccessResult::forbidden('This legal entity is not editable.');
    }

    return AccessResult::allowed();
  }

}
