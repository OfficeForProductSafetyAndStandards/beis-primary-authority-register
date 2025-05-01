<?php

namespace Drupal\par_reporting\Plugin\ParStatistic;

use Drupal\par_reporting\ParStatisticBase;

/**
 * Cease a member.
 *
 * @ParStatistic(
 *   id = "total_enquiries",
 *   title = @Translation("Total enquiries."),
 *   description = @Translation("The total number of enquiry messages submitted."),
 *   status = TRUE,
 * )
 */
class TotalEnquiryMessages extends ParStatisticBase {

  #[\Override]
  public function getStat(): int {
    $query = $this->getParDataManager()->getEntityQuery('par_data_general_enquiry');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    return $query->count()->execute();
  }

}
