<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
* Checks that the submitted date is in the past.
 *
 * If a comparative date is set the value must be before this.
*
* @Constraint(
*   id = "past_date",
*   label = @Translation("Past Date", context = "Validation"),
* )
*/
class PastDate extends LessThanOrEqual {

  const CONVERSION_ERROR = '921554ef-3718-47fe-b51c-ba7c4cf13278';

  public $message = 'The date should be in the past.';

  /**
   * @return string
   *   The full class name for the constraint validator.
   */
  public function validatedBy() {
    return get_class($this) . 'Validator';
  }

}
