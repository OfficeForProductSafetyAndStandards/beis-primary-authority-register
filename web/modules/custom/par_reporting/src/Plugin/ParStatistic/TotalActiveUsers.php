<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "active_users",
 *   title = @Translation("Active users."),
 *   status = TRUE,
 * )
 */
class TotalActiveUsers extends ParStatisticBase {

  #[\Override]
  public function getStat(): int {
    $query = $this->getParDataManager()->getEntityQuery('user')
      ->condition('status', 1)
      ->condition('access', strtotime("-12 months"), '>=');

    return $query->count()->execute();
  }

}
