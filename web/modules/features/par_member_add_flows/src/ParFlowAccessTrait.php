<?php

namespace Drupal\par_member_add_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;

trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL) {
    // Ensure that access callbacks use the correct parameters.
    $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);

    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      $this->accessResult = AccessResult::forbidden('This is not a coordinated partnership.');
    }

    $locked = FALSE;

    // If the member upload is in progress the member list cannot be modified.
    if ($locked) {
      $this->accessResult = AccessResult::forbidden('This member list is locked because an upload is in progress.');
    }

    return parent::accessCallback();
  }
}
