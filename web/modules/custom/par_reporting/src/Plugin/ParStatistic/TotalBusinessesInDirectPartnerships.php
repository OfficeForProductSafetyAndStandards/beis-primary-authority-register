<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_direct_businesses",
 *   title = @Translation("Businesses in direct partnerships."),
 *   status = TRUE,
 * )
 */
class TotalBusinessesInDirectPartnerships extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');

    $query->condition('partnership_type', 'direct', 'CONTAINS');

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

    $entities = $query->execute();
    $partnerships = $this->getEntityTypeManager()->getStorage('par_data_partnership')->loadMultiple(array_unique($entities));

    $total = 0;
    foreach ($partnerships as $partnership) {
      $count = 0;
      $legal_entities = $partnership->getLegalEntity();

      // Count how many legal entities are covered under this direct partnership.
      if ($legal_entities && count($legal_entities) >= 1) {
        $count = count($legal_entities);
      }
      else {
        $count = 1;
      }

      $total += $count;
    }

    return $total;
  }

}
