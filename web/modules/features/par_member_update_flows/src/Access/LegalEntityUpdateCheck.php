<?php

namespace Drupal\par_member_update_flows\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Checks access for updating legal entities on the coordinated member.
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
   *   Data Manager Service.
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $flow_negotiator
   *   Flow Negotiator Service.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, ParFlowNegotiatorInterface $flow_negotiator) {
    $this->parDataManager = $par_data_manager;
    $this->flowNegotiator = $flow_negotiator;
  }

  /**
   * Get the Par Flow Negotiator.
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return $this->flowNegotiator;
  }

  /**
   * Get the Par Data Manager.
   */
  public function getParDataManager(): ParDataManagerInterface {
    return $this->parDataManager;
  }

  /**
   * Check the access.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL, ParDataLegalEntity $par_data_legal_entity = NULL) {
    try {
      // New flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      return AccessResult::forbidden('This is not a coordinated partnership.');
    }

    // If the membership has been ceased we won't let them re-edit.
    if ($par_data_coordinated_business->isRevoked()) {
      return AccessResult::forbidden('This member has been ceased you cannot change their details.');
    }

    // If the member upload is in progress the member list cannot be modified.
    if ($par_data_partnership->isMembershipLocked()) {
      return AccessResult::forbidden('This member list is locked because an upload is in progress.');
    }

    // Check the user has permission to manage the current organisation.
    if (!$account->hasPermission('bypass par_data membership')
      && !$this->getParDataManager()->isMember($par_data_partnership->getOrganisation(TRUE), $user)) {
      return AccessResult::forbidden('User does not have permissions to remove authority contacts from this partnership.');
    }

    // Only legacy legal entities can be updated.
    if (!$par_data_legal_entity?->isEditable()) {
      return AccessResult::forbidden('This legal entity is not editable.');
    }

    return AccessResult::allowed();
  }

}
