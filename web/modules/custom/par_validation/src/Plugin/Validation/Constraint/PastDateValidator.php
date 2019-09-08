<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Drupal\Core\Datetime\DrupalDateTime;
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
        $date = \DateTime::createFromFormat("d/m/Y H:i:s", $value . " 23:59:59");
        if (empty($date) || !$date instanceof \DateTimeInterface) {
          throw new \Exception('The past date validator could not covert the string to a date.');
        }
      }
      catch (\Exception $e) {
        $this->context->buildViolation($constraint->message)
          ->setCode(PastDate::CONVERSION_ERROR)
          ->addViolation();
      }
    }

    if (isset($date) && $date instanceof \DateTimeInterface) {
      parent::validate($date, $constraint);
    }
  }

}
