<?php

namespace Drupal\par_partnership_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

trait ParPartnershipFlowAccessTrait {

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   *
   * @TODO Please be aware that this access callback is currently specific to
   * the ParPartnershipFlowsLegalEntityForm class and would need to be updated
   * for use with other forms in par_partnership_flows flows.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    // Limit access to partnership pages.
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;
    if (!$account->hasPermission('bypass par_data membership') && $user && !$this->getParDataManager()->isMember($par_data_partnership, $user)) {
      $this->accessResult = AccessResult::forbidden('The user is not allowed to access this page.');
    }

    switch ($route_match->getRouteName()) {
      case 'par_partnership_flows.advice_add':
      case 'par_partnership_flows.advice_upload_documents':
        // Restrict advice upload to active partnerships only.
        if (!$par_data_partnership->isActive()) {
          $this->accessResult = AccessResult::forbidden('Advice can only be added to active partnerships.');
        }
        break;
      case 'par_partnership_flows.legal_entity_add':
      case 'par_partnership_flows.legal_entity_edit':
        // Restrict access to partnerships that haven't yet been nominated.
        if ($par_data_partnership->isActive()) {
          $this->accessResult = AccessResult::forbidden('This partnership is active therefore the legal entities cannot be changed.');
        }
        // Also restrict business users who have already conirmed their business details.
        if ($par_data_partnership->getRawStatus() === 'confirmed_business' && !$account->hasPermission('approve partnerships')) {
          $this->accessResult = AccessResult::forbidden('This partnership has been confirmed by the business therefore the legal entities cannot be changed.');
        }
        break;
      case 'par_partnership_flows.advice_edit':
      case 'par_partnership_flows.advice_edit_documents':
        // Restrict editorial access to archived and deleted advice entities.
        if ($par_data_advice->isArchived() || $par_data_advice->isDeleted()) {
          $this->accessResult = AccessResult::forbidden('This advice has been archived or deleted and therefore cannot be edited.');
        }
        break;
      case 'par_partnership_flows.advice_archive':
        // Restrict editorial access to archived and deleted advice entities.
        if ($par_data_advice->isArchived() || $par_data_advice->isDeleted()) {
          $this->accessResult = AccessResult::forbidden('This advice is already has been archived or deleted.');
        }
        break;
      case 'par_partnership_flows.inspection_plan_upload':
      case 'par_partnership_flows.inspection_plan_add':
      case 'par_partnership_flows.inspection_plan_add_date':
        if (!$par_data_partnership->isActive()) {
          $this->accessResult = AccessResult::forbidden('Inspection plans can only be added to active partnerships.');
        }
        break;
      case 'par_partnership_flows.inspection_plan_revoke':
      case 'par_partnership_flows.inspection_plan_edit':
      case 'par_partnership_flows.inspection_plan_edit_date':
        // Restrict editorial access to revoked and deleted inspection plan entities.
        if ($par_data_inspection_plan->isRevoked() || $par_data_inspection_plan->isDeleted()) {
          $this->accessResult = AccessResult::forbidden('This inspection plan has been revoked or deleted and therefore cannot be edited.');
        }
        break;
      }

    return parent::accessCallback($route, $route_match, $account);
  }
}
