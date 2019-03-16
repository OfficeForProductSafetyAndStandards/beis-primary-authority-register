<?php

namespace Drupal\par_partnership_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParFlowException;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    switch ($route_match->getRouteName()) {
      case 'par_partnership_flows.advice_add':
      case 'par_partnership_flows.advice_upload_documents':
        if ($par_data_partnership->inProgress()) {
          $this->accessResult = AccessResult::forbidden('Advice can only be added to active partnerships.');
        }
        break;
      case 'par_partnership_flows.legal_entity_add':
      case 'par_partnership_flows.legal_entity_edit':
        // Restrict access to active partnerships.
        if (!$par_data_partnership->inProgress()) {
          $this->accessResult = AccessResult::forbidden('This partnership is active therefore the legal entity cannot be added.');
        }
        break;
      }
    return parent::accessCallback($route, $route_match, $account);
  }
}
