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

      if (!isset($item->value) || false === $item->value || (empty($item->value) && '0' != $item->value)) {
        $this->context->addViolation($constraint->message, ['%value' => $item->value]);
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
