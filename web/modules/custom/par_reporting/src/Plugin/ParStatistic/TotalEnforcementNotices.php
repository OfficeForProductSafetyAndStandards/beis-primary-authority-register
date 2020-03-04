<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_enforcement_notices",
 *   title = @Translation("Total enforcement notices."),
 *   description = @Translation("The total number of enforcement notices created by enfrocement officers."),
 *   status = TRUE,
 * )
 */
class TotalEnforcementNotices extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_enforcement_notice');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    return $query->count()->execute();
  }

}
