<?php

namespace Drupal\par_data\Plugin\views\field;

use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\user\UserInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get the email address associated with the person's user account.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_person_account_email")
 */
class ParPersonAccountEmail extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return TRUE;
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $entity = $this->getEntity($values);

    if ($entity instanceof ParDataPersonInterface) {
      // If the person has a user account with a different email address
      // then this email address will be used.
      $account = $entity->retrieveUserAccount();

      return $account instanceof UserInterface ? strtolower($account->getEmail()) : strtolower($entity->getEmail());
    }
  }

}
