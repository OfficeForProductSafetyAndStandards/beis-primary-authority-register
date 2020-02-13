<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_revoked_partnerships",
 *   title = @Translation("Revoked partnerships."),
 *   description = @Translation("The total number of partnerships that have been revoked."),
 *   status = TRUE,
 * )
 */
class TotalRevokedPartnerships extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('revoked', 1);

    return $query->count()->execute();
  }

}
