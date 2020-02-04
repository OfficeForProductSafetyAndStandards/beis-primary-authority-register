<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_primary_authorities",
 *   title = @Translation("Primary authorities."),
 *   status = TRUE,
 * )
 */
class TotalPrimaryAuthorities extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');

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

    $authorities = [];
    foreach ($partnerships as $partnership) {
      $primary_authority = $partnership->getAuthority(TRUE);

      if ($primary_authority && !isset($authorities[$primary_authority->uuid()])) {
        $authorities[$primary_authority->uuid()] = $primary_authority->label();
      }
    }

    return count($authorities);
  }

}
