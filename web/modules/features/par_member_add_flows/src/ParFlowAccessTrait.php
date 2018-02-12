<?php

namespace Drupal\par_member_add_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;

trait ParFlowAccessTrait {

  /**
  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL) {
    $locked = FALSE;

    // If the member upload is in progress the member list cannot be modified.
    if ($locked) {
      $this->accessResult = AccessResult::forbidden('This member list is locked because an upload is in progress.');
    }

    return parent::accessCallback();
  }
}
