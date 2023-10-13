<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_coordinated_members",
 *   title = @Translation("Businesses in coordinated partnerships."),
 *   description = @Translation("The total number of legal entities covered by a coordinated partnerships. If a legal entity is covered by two partnerships it will be counted twice."),
 *   status = TRUE,
 * )
 */
class TotalCoordinatedMembers extends ParStatisticBase {

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

    $entities = array_unique($query->execute());
    $partnerships = !empty($entities) ?
      $this->getEntityTypeManager()->getStorage('par_data_partnership')->loadMultiple($entities) :
      [];

    $total = 0;
    foreach ($partnerships as $partnership) {
      $count = 0;

      switch ($partnership->getMemberDisplay()) {
        // If counting an internal member list count all the legal entities for each members as unique.
        case ParDataPartnership::MEMBER_DISPLAY_INTERNAL:
          foreach ($partnership->getCoordinatedMember() as $member) {
            $count += $member->get('field_legal_entity')?->count() ?? 1;
          }

          break;
        // If counting external lists use the default counting method.
        case ParDataPartnership::MEMBER_DISPLAY_EXTERNAL:
        case ParDataPartnership::MEMBER_DISPLAY_REQUEST:
          $count = $partnership->numberOfMembers();

          break;
        // If the member list hasn't been updated since release `v63.0` settle on a default value.
        default:
          $count = 1;
      }

      $total += $count;
    }

    return $total;
  }

}
