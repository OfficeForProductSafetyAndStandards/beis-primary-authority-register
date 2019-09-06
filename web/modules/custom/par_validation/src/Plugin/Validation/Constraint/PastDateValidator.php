<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqualValidator;

/**
* Validates the past date constraint.
*/
class PastDateValidator extends LessThanOrEqualValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (\is_string($value)) {
      // Convert the value to a datetime for comparison.
      try {
        $convertedValue = new \DateTime($value);
      }
      catch (\Exception $e) {
        $this->context->buildViolation($constraint->conversion_error)
          ->setCode(PastDate::CONVERSION_ERROR)
          ->addViolation();
      }
    }

    if (isset($convertedValue) && $convertedValue instanceof \DateTimeInterface) {
      parent::validate($convertedValue, $constraint);
    }
  }

}
