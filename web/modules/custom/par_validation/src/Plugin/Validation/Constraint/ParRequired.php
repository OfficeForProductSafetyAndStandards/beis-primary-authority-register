<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is required, if within the context of PAR.
 *
 * @Constraint(
 *   id = "par_required",
 *   label = @Translation("PAR Required", context = "Validation"),
 * )
 */
class ParRequired extends Constraint {

  /**
   * The message for users that have not entered a value.
   */
  public $message = 'You must fill in the missing information.';

  /**
   * The properties that we want to validate.
   */
  public $properties = ['value'];

  /**
   * @return string
   *   The full class name for the constraint validator.
   */
  public function validatedBy() {
    return get_class($this) . 'Validator';
  }

}
