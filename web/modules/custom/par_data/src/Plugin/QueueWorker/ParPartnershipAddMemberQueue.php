<?php

namespace Drupal\Learning\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Processes coordinated business members and adds them to a partnership.
 *
 * @QueueWorker(
 *   id = "par_partnership_add_members",
 *   title = @Translation("PAR Partnership - Add Members"),
 *   cron = {"time" = 60}
 * )
 */
class ParPartnershipAddMemberQueue extends QueueWorkerBase {
  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $partnership = ParDataPartnership::load($data['partnership']);

    // Adding a new legal entity.
    $legal_entity = ParDataLegalEntity::create([
      'type' => 'legal_entity',
      'uid' => 1,
    ]);
    foreach ($this->getColumns()['par_data_legal_entity'] as $field => $column) {
      $legal_entity->set($field, $this->getRowValue($member, $column));
    }
    var_dump($legal_entity->get('registered_name')->getString());
    $legal_entity->save();

  }
}
