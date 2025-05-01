<?php

namespace Drupal\par_validation\Plugin\Validation\Constraint;

use Drupal\Core\Field\FieldItemInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ParRequiredConstraint constraint.
 */
class ParRequiredValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validate($value, Constraint $constraint): void {
    // Check to make sure there are field values.
    if ($value->isEmpty() && !$this->skipValidate()) {
      $this->context->addViolation($constraint->message, ['@value' => $value->getName()]);
    }

    // Check to make sure all the field values are not empty.
    foreach ($value as $item) {
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
          $property = $item instanceof FieldItemInterface ? $item->mainPropertyName() : $value;
        }

        if (!isset($item->$property) || FALSE === $item->$property || (empty($item->$property) && '0' != $item->$property)) {
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
