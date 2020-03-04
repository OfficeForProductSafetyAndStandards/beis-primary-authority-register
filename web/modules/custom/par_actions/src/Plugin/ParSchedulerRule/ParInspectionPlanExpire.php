<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "expiry_notification_inspection_plan",
 *   title = @Translation("Warning notification of inspection plan expiry."),
 *   entity = "par_data_inspection_plan",
 *   property = "valid_date.end_value",
 *   time = "-3 months",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_send_expiry_notice"
 * )
 */
class ParInspectionPlanExpire extends ParSchedulerRuleBase {

  public function query() {
    $query = parent::query();

    $query->condition('inspection_status', 'current');

    return $query;
  }
}
