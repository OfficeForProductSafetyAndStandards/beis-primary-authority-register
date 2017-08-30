<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\user\Entity\User;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The base form controller for all PAR forms.
 */
abstract class ParPartnershipBaseForm extends ParBaseForm implements ParBaseInterface {

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
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
//      }
//      else if ($par_data_manager->isMemberOfBusiness($account))
//      {
//
//        $this->flow = 'partnership_direct';
//      }
//    }
//  }

    return isset($this->flow) ? $this->flow : '';
  }
}
