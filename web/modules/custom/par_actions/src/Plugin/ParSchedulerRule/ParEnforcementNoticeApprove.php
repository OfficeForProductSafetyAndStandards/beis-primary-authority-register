<?php

namespace Drupal\par_actions\Plugin\QueueWorker;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "approve_enforcement",
 *   title = @Translation("Auto-approval of enforcement notices."),
 *   cron = {"time" = 15},
 *   entity = "par_enforcement_notice",
 *   property = "created",
 *   time = "-5 days"
 * )
 */
class ParEnforcementNoticeApprove extends ParSchedulerRuleBase {

}
