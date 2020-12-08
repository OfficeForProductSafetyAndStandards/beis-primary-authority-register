<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "active_partnerships_with_inspection_plans",
 *   title = @Translation("Active partnerships with inspection plans."),
 *   description = @Translation("The number of active partnerships with active inspection plans."),
 *   status = TRUE,
 * )
 */
class ActivePartnershipsWithInspectionPlans extends ParStatisticBase {

  public function getStat() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');
//      ->condition('field_inspection_plan', NULL, 'IS NOT NULL');

    $inspection_plan_revocation_status = $query
      ->orConditionGroup()
      ->condition('field_inspection_plan.entity:par_data_inspection_plan.revoked', 0)
      ->condition('field_inspection_plan.entity:par_data_inspection_plan.revoked', NULL, 'IS NOT NULL');
    $inspection_plan_deletion_status = $query
      ->orConditionGroup()
      ->condition('field_inspection_plan.entity:par_data_inspection_plan.deleted', 0)
      ->condition('field_inspection_plan.entity:par_data_inspection_plan.deleted', NULL, 'IS NOT NULL');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($inspection_plan_revocation_status);
    $query->condition($inspection_plan_deletion_status);
    $query->condition($revoked);
    $query->condition($deleted);


    return $query->count()->execute();
  }

}
