<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_active_coordinated_partnerships",
 *   title = @Translation("Active coordinated partnership applications."),
 *   description = @Translation("The number of active coordinated partnerships. Each partnership is recorded once regarldess of how many businesses it covers."),
 *   status = TRUE,
 * )
 */
class TotalActiveCoordinatedPartnerships extends ParStatisticBase {

  public function getStat(): int {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');

    $query->condition('partnership_type', 'coordinated', 'CONTAINS');

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
