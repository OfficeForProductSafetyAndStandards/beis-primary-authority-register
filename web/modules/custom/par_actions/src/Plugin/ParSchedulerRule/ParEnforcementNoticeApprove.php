<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "approve_enforcement",
 *   title = @Translation("Auto-approval of enforcement notices."),
 *   cron = {"time" = 15},
 *   entity = "par_data_enforcement_notice",
 *   property = "notice_date",
 *   time = "-5 days",
 *   action = "cron_enforcement_notice_action_auto_approval"
 * )
 */
class ParEnforcementNoticeApprove extends ParSchedulerRuleBase {

  public function query() {
    $query = parent::query();

    $query->condition('field_enforcement_action.entity.primary_authority_status', 'awaiting_approval');

    return $query;
  }
}
