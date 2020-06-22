<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

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
class ParPersonEmail extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
    parent::query();
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

      return $account instanceof UserInterface ? $account->getEmail() : $entity->getEmail();
    }
  }
}
