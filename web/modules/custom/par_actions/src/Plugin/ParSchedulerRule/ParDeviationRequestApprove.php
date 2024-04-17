<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Approve a deviation request.
 *
 * @ParSchedulerRule(
 *   id = "approve_deviation",
 *   title = @Translation("Auto-approval of deviation requests."),
 *   entity = "par_data_deviation_request",
 *   property = "request_date",
 *   time = "-6 working days",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_entity_approve"
 * )
 */
class ParDeviationRequestApprove extends ParSchedulerRuleBase {

  /**
   *
   */
  public function query() {
    $query = parent::query();

    $query->condition('primary_authority_status', 'awaiting');

    return $query;
  }

}
