<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;
use Drupal\par_data\Entity\ParDataLegalEntity;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "legal_entity_registry_update",
 *   title = @Translation("Daily syncing of legal entities with external registeries."),
 *   entity = "par_data_legal_entity",
 *   frequency = "1 day",
 *   queue = TRUE,
 *   status = FALSE,
 *   action = "par_update_registered_legal_entity"
 * )
 */
class ParLegalEntityUpdate extends ParSchedulerRuleBase {

  /**
 *
 */
  #[\Override]
  public function query() {
    $query = parent::query();

    $time = \Drupal::time()->getRequestTime();

    // Only act on entities that have a valid registry set.
    $query->condition('registry', NULL, 'IS NOT NULL');
    $query->condition('registry', ParDataLegalEntity::DEFAULT_REGISTER, '!=');

    return $query;
  }

}
