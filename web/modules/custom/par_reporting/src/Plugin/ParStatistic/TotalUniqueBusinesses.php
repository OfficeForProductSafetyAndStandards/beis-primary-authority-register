<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_reporting\ParStatisticBase;

/**
 * Total unique businesses in a partnership.
 *
 * @ParStatistic(
 *   id = "total_unique_businesses",
 *   title = @Translation("Total unique businesses in partnership."),
 *   description = @Translation("The total number of unique legal entities covered by a partnership. Each legal entity will only be counted once."),
 *   status = TRUE,
 * )
 */
class TotalUniqueBusinesses extends ParStatisticBase {

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

    $entities = array_unique($query->execute());
    $partnerships = !empty($entities) ?
      $this->getEntityTypeManager()->getStorage('par_data_partnership')->loadMultiple($entities) :
      [];

    $count = [];
    $external_count = 0;
    foreach ($partnerships as $partnership) {
      // DIRECT PARTNERSHIP.
      if ($partnership->isDirect()) {
        $legal_entities = $partnership->getLegalEntity();

        // Get a list of all legal entities covered by this partnership keyed by a unique key.
        foreach ($legal_entities as $legal_entity) {
          // Most but not all legal entities have a registered number, those that don't can't be de-duped.
          $key = !$legal_entity->get('registered_number')->isEmpty() ?
            $legal_entity->get('registered_number')->getString() :
            $legal_entity->id();
          $entities[$key] = $legal_entity->label();
        }

        // If there were no legal entities just count the partnership once.
        if (!$legal_entities or count($legal_entities) <= 0) {
          $count[] = $partnership->label();
        }
      }

      // COORDINATED PARTNERSHIP.
      if ($partnership->isCoordinated()) {
        switch ($partnership->getMemberDisplay()) {
          // If counting an internal member list count all the legal entities.
          case ParDataPartnership::MEMBER_DISPLAY_INTERNAL:
            foreach ($partnership->getCoordinatedMember() as $member) {
              $legal_entities = $member->getLegalEntity();

              // Get a list of all legal entities covered by this partnership keyed by a unique key.
              foreach ($legal_entities as $legal_entity) {
                // Most but not all legal entities have a registered number, those that don't can't be de-duped.
                $key = !$legal_entity->get('registered_number')->isEmpty() ?
                  $legal_entity->get('registered_number')->getString() :
                  $legal_entity->id();
                $count[$key] = $legal_entity->label();
              }

              // If there were no legal entities just count the member once.
              if (!$legal_entities or count($legal_entities) <= 0) {
                $count[] = $member->label();
              }
            }

            break;
          // If counting external lists just count the partnership once.
          case ParDataPartnership::MEMBER_DISPLAY_EXTERNAL:
          case ParDataPartnership::MEMBER_DISPLAY_REQUEST:
            $external_count += $partnership->numberOfMembers();

            break;
          // If the member list hasn't been updated since release `v63.0` just count the partnership once.
          default:
            $count[] = $partnership->label();
        }
      }
    }

    return count($count) + $external_count;
  }

}
