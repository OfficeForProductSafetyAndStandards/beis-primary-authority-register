<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Unique direct businesses.
 *
 * @ParStatistic(
 *   id = "total_unique_direct_businesses",
 *   title = @Translation("Businesses in direct partnerships."),
 *   description = @Translation("The total number of unique legal entities covered by a direct partnership. Each legal entity will only be counted once."),
 *   status = FALSE,
 * )
 */
class TotalUniqueBusinessesInDirectPartnerships extends ParStatisticBase {

  /**
 *
 */
  #[\Override]
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

    $count = [];
    foreach ($partnerships as $partnership) {
      $legal_entities = $partnership->getLegalEntity();

      // Get a list of all legal entities covered by this partnership keyed by a unique key.
      foreach ($legal_entities as $legal_entity) {
        // Most but not all legal entities have a registered number, those that don't can't be de-duped.
        $key = !$legal_entity->get('registered_number')->isEmpty() ?
          $legal_entity->get('registered_number')->getString() :
          $legal_entity->id();
        $count[$key] = $legal_entity->label();
      }

      // If there were no legal entities just count the partnership once.
      if (!$legal_entities or count($legal_entities) <= 0) {
        $count[] = $partnership->label();
      }
    }

    return count($count);
  }

}
