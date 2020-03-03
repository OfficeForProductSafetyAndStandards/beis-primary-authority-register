<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Cease a member.
 *
 * @ParSchedulerRule(
 *   id = "test_past",
 *   title = @Translation("Test automatic expiry of past entities."),
 *   entity = "par_data_test_entity",
 *   property = "expiry_date",
 *   time = "0 days",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_test_action"
 * )
 */
class ParExpiryTestPast extends ParSchedulerRuleBase {

}
