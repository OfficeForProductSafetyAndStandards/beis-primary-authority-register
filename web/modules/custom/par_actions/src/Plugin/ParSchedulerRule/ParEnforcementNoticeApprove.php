<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "approve_enforcement",
 *   title = @Translation("Auto-approval of enforcement notices."),
 *   entity = "par_data_enforcement_notice",
 *   property = "notice_date",
 *   time = "+6 days",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_action_approve"
 * )
 */
class ParEnforcementNoticeApprove extends ParSchedulerRuleBase {

  public function query() {
    $query = parent::query();

    $query->condition('field_enforcement_action.entity.primary_authority_status', 'awaiting_approval');

    return $query;
  }
}
