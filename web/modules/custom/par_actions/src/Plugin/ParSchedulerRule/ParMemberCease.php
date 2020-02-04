<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParSchedulerRule(
 *   id = "cease_member",
 *   title = @Translation("Auto-cease of member on membership end date."),
 *   entity = "par_data_coordinated_business",
 *   property = "date_membership_ceased",
 *   time = "0 days",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_action_cease"
 * )
 */
class ParMemberCease extends ParStatisticBase {

}
