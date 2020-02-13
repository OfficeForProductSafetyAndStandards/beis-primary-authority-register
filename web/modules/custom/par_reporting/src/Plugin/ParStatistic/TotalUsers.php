<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_users",
 *   title = @Translation("Total users."),
 *   description = @Translation("The total number of user accounts."),
 *   status = TRUE,
 * )
 */
class TotalUsers extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('user')
      ->condition('status', 1);

    return $query->count()->execute();
  }

}
