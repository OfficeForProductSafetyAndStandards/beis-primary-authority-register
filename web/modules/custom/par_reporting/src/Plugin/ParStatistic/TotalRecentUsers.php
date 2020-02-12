<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "recent_users",
 *   title = @Translation("Recent users."),
 *   description = @Translation("Users that have been active within the last month."),
 *   status = TRUE,
 * )
 */
class TotalRecentUsers extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('user')
      ->condition('status', 1)
      ->condition('access', strtotime("-1 months"), '>=');

    return $query->count()->execute();
  }

}
