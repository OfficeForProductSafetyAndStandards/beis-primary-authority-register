<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_direct_businesses",
 *   title = @Translation("Businesses in direct partnerships."),
 *   description = @Translation("The total number of legal entities covered by a direct partnership. If a legal entity is covered by two partnerships it will be counted twice."),
 *   status = TRUE,
 * )
 */
class TotalBusinessesInDirectPartnerships extends ParStatisticBase {

  public function getStat(): int {
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

    $entities = array_unique($query->execute());
    $partnerships = !empty($entities) ?
      $this->getEntityTypeManager()->getStorage('par_data_partnership')->loadMultiple($entities) :
      [];

    $total = 0;
    foreach ($partnerships as $partnership) {
      $total += $partnership->get('field_partnership_legal_entity')?->count();
    }

    return $total;
  }

}
