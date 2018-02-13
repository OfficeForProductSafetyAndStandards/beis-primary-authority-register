<?php

namespace Drupal\par_partnership_confirmation_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\par_data\Entity\ParDataPartnership;

trait ParFlowAccessTrait {

  /**
  /**
   * {@inheritdoc}
   */
  public function accessCallback(ParDataPartnership $par_data_partnership = NULL) {
    // Ensure that access callbacks use the correct parameters.
    $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);

    $allowed_statuses = [
      $par_data_partnership->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
    ];

    // If this enforcement notice has not been reviewed.
    if (!in_array($par_data_partnership->getRawStatus(), $allowed_statuses)) {
      $this->accessResult = AccessResult::forbidden('This partnership has not been fully reviewed yet.');
    }

    return parent::accessCallback();
  }
}
