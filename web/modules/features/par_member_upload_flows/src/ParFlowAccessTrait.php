<?php

namespace Drupal\par_member_upload_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;

trait ParFlowAccessTrait {

  /**
  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL) {
    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      $this->accessResult = AccessResult::forbidden('This is not a coordianted partnership.');
    }

    return parent::accessCallback();
  }
}
