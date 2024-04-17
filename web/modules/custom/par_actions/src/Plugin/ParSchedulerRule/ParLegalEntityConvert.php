<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "legal_entity_registry_convert",
 *   title = @Translation("Convert legacy legal entities."),
 *   entity = "par_data_legal_entity",
 *   frequency = "1 day",
 *   queue = TRUE,
 *   status = FALSE,
 *   action = "par_convert_legacy_legal_entity"
 * )
 */
class ParLegalEntityConvert extends ParSchedulerRuleBase {

  /**
   *
   */
  public function query() {
    $query = parent::query();

    // Only act on entities that have a valid registry set.
    $query->condition('registry', NULL, 'IS NULL');

    return $query;
  }

}
