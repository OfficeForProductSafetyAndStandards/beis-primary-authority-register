<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_businesses",
 *   title = @Translation("Total businesses in partnership."),
 *   description = @Translation("The total number of legal entities covered by a partnership. If a legal entity is covered by two partnerships it will be counted twice."),
 *   status = TRUE,
 * )
 */
class TotalBusinesses extends ParStatisticBase {

  /**
   * Implements getStat function.
   */
  public function getStat(): int {
    // This is a combination of direct and coordinated statistics.
    $direct = $this->getReportingManager()->get('total_direct_businesses');
    $coordinated = $this->getReportingManager()->get('total_coordinated_members');

    return $direct + $coordinated;
  }

}
