<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
* Validates the ParRequiredConstraint constraint.
*/
class ParRequiredValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      // Ignore validating any users that have the bypass permission.
      if ($this->skipValidate()) {
        break;
      }
      foreach ($constraint->properties as $key => $value) {
        if (isset($key) && is_string($key)) {
          $message = $value['message'];
          $property = $key;
        }
        else {
          $message = $constraint->message;
          $property = $value;
        }

        if (!isset($item->$property) || false === $item->$property || (empty($item->$property) && '0' != $item->$property)) {
          $this->context->addViolation($message, ['@value' => $item->$property]);
        }
      }
    }
  }

  /**
   * Determine the contexts under which this validation is appropriate.
   */
  public function skipValidate() {
    $current_user = \Drupal::currentUser();
    return $current_user->hasPermission('par bypass validation');
  }

}
