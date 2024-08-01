<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_authorities",
 *   title = @Translation("Total authorities."),
 *   description = @Translation("The total number of authorities in the system."),
 *   status = TRUE,
 * )
 */
class TotalAuthorities extends ParStatisticBase {

  /**
   * Implements getStat function.
   */
  public function getStat(): int {
    $query = $this->getParDataManager()->getEntityQuery('par_data_authority');

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
