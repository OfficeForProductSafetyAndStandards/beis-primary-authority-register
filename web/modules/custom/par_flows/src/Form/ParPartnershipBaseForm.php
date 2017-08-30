<?php

namespace Drupal\par_flows\Form;

use Drupal\user\Entity\User;
use Drupal\par_flows\ParBaseInterface;

/**
 * The base form controller for all PAR forms.
 */
abstract class ParPartnershipBaseForm extends ParBaseForm implements ParBaseInterface {

  /**
   * Get the current flow entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The flow entity.
   */
//  public function getFlow() {
//    $account = User::Load($this->currentUser()->id());
//    if ($account->hasRole('par_authority')) {
//      return 'authority_flow';
//    }
//
//    if ($par_data_partnership = $this->getRouteParam('par_data_partnership')) {
//      $partnership_type = $par_data_partnership->get('partnership_type')->getString();
//      if ($partnership_type === 'coordinated') {
//        return $this->getFlowStorage()->load('coordinated_flow');
//      }
//      if ($partnership_type === 'business') {
//        return 'organisation_flow';
//      }
//
//    }
//    return $this->getFlowStorage()->load($this->getFlowName());
//  }


  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.   */
  public function getFlowName() {
    $account = User::Load($this->currentUser()->id());

//    // Get the partnership from the route.
//    $par_data_partnership = $this->getRouteParam('par_data_partnership')
//
//    // Lookup flows by route.
//    if (!$flow) {
//      // Check for Partnership type.
//      if ($this->currentUser()->hasPermission('bypass par_data access')) {
//        $this->flow = 'partnership_helpdesk';
//      }
//      else if ($par_data_manager->isMemberOfAuthority($account)) {
//        $this->flow = 'partnership_authority';
//      }
//      else if ($par_data_manager->isMemberOfCoordinator($account))
//      {
        $this->flow = 'partnership_coordinated';
//      }
//      else if ($par_data_manager->isMemberOfBusiness($account))
//      {
//
//        $this->flow = 'partnership_direct';
//      }
//    }
    return $this->flow;
  }

//  return isset($this->flow) ? $this->flow : '';
}
