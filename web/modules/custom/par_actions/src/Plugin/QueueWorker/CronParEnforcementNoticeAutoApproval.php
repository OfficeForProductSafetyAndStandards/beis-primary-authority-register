<?php

namespace Drupal\par_actions\Plugin\QueueWorker;

/**
* An auto approver of actions.
*
* @QueueWorker(
*   id = "cron_enforcement_notice_action_auto_approval",
*   title = @Translation("Cron Enforcement Notice actions auto-approval."),
*   cron = {"time" = 100}
* )
*/
class CronParEnforcementNoticeAutoApproval extends ParEnforcementNoticeAutoApproval {}
