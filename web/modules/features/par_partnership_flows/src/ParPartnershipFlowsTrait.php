<?php

namespace Drupal\par_partnership_flows;

/**
 * The base form controller for all PAR forms.
 */

trait ParPartnershipFlowsTrait {

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
  public function getFlowName() {
    // Use static here.
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
//      }
//      else if ($par_data_manager->isMemberOfBusiness($account))
//      {
//
//        $this->flow = 'partnership_direct';
//      }
//    }
    $this->flow = 'partnership_direct';
    $this->flow = 'partnership_coordinated';
    return isset($this->flow) ? $this->flow : '';
  }
}
