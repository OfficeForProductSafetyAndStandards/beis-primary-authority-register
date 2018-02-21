<?php

namespace Drupal\par_member_cease_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;

trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL, ParDataCoordinatedBusiness $par_data_coordinated_business = NULL) {
    // If the partnership isn't a coordinated one then don't allow update.
    if (!$par_data_partnership->isCoordinated()) {
      $this->accessResult = AccessResult::forbidden('This is not a coordinated partnership.');
    }

    return parent::accessCallback();
  }
}
