<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Active advice statistic.
 *
 * @ParStatistic(
 *   id = "active_advice",
 *   title = @Translation("Active advice attached to a partnership."),
 *   description = @Translation("The number of active pieces of advice. Advice with multiple documents is only counted once."),
 *   status = TRUE,
 * )
 */
class TotalActiveAdvice extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_advice');

    $archived = $query
      ->orConditionGroup()
      ->condition('archived', 0)
      ->condition('archived', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($archived);
    $query->condition($deleted);

    return $query->count()->execute();
  }

}
