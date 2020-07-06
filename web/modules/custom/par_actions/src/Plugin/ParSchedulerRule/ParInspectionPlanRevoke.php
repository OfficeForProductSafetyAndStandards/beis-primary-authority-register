<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Revoke an inspection plan.
 *
 * @ParSchedulerRule(
 *   id = "revoke_inspection_plan",
 *   title = @Translation("Auto-revocation of inspection plans on expiry date."),
 *   entity = "par_data_inspection_plan",
 *   property = "valid_date.end_value",
 *   time = "0 days",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_action_revoke"
 * )
 */
class ParInspectionPlanRevoke extends ParSchedulerRuleBase {

  public function query() {
    $query = parent::query();

    $query->condition('inspection_status', 'current');

    return $query;
  }

}
