<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "legal_entity_update",
 *   title = @Translation("Weekly syncing of legal entities with Companies House."),
 *   entity = "par_data_legal_entity",
 *   frequency = "1 week",
 *   queue = TRUE,
 *   status = FALSE,
 *   action = "par_update_legal_entity_name"
 * )
 */
class ParLegalEntityUpdate extends ParSchedulerRuleBase {

  public function query() {
    $query = parent::query();

    $time = \Drupal::time()->getRequestTime();

    // Get all entities that haven't been updated in a week.
    $query->condition('updated', $time-(60*60*24*6), '<');

    return $query;
  }
}
