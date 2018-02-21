<?php

namespace Drupal\par_member_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;

trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL) {
    // Ensure that access callbacks use the correct parameters.
    $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    $this->getFlowDataHandler()->setParameter('par_data_coordinated_business', $par_data_coordinated_business);
    // Reload the data to set the organisation parameters.
    $this->loadData();

    $locked = FALSE;

    // If the member upload is in progress the member list cannot be modified.
    if ($locked) {
      $this->accessResult = AccessResult::forbidden('This member list is locked because an upload is in progress.');
    }
    // If the membership has been ceased we won't let them re-edit.
    if ($par_data_coordinated_business->isRevoked()) {
      $this->accessResult = AccessResult::forbidden('This member has been ceased you cannot change their details.');
    }

    return parent::accessCallback();
  }
}
