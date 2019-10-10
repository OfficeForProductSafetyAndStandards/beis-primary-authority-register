<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/**
* Checks that the submitted date is in the future.
 *
 * If a comparative date is set the value must be after this.
*
* @Constraint(
*   id = "future_date",
*   label = @Translation("Future Date", context = "Validation"),
* )
*/
class FutureDate extends GreaterThanOrEqual {

  const CONVERSION_ERROR = '520edb7b-644b-4097-a426-bde4a24edcc3';

  public $message = 'The date should be in the future.';

  /**
   * @return string
   *   The full class name for the constraint validator.
   */
  public function validatedBy() {
    return get_class($this) . 'Validator';
  }

}
