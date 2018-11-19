<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\par_data\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get the PAR Data status author.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_data_status_author")
 */
class ParDataStatusAuthor extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Get the par data manager.
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $entity = $this->getEntity($values);

    if ($entity instanceof ParDataEntityInterface) {
      $author = $entity->getStatusAuthor($entity->getRawStatus());
      $label = $author ? $author->label() : '';

      if ($contacts = $this->getParDataManager()->getUserPeople($author)) {
        $contact = current($contacts);
        $label = $contact->label();
      }

      // If the uid is that of the admin user this has been automatically approved.
      if ($author->id() <= 1) {
        $label = '(automatically approved)';
      }

      return $label;
    }
  }
}
