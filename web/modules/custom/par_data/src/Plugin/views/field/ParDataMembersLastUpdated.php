<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\par_data\Plugin\views\field;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get display the users who are considered members of an entity.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_data_members_last_updated")
 */
class ParDataMembersLastUpdated extends FieldPluginBase {

  /**
   * Get the date formatter.
   */
  public function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * @{inheritdoc}
   */
  #[\Override]
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * @{inheritdoc}
   */
  #[\Override]
  public function render(ResultRow $values) {
    $entity = $this->getEntity($values);

    if ($entity instanceof ParDataPartnership && $entity->isCoordinated()) {
      $last_updated = $entity->membersLastUpdated();
      return $last_updated ? $this->getDateFormatter()->format($last_updated, 'gds_date_format') : NULL;
    }

    return NULL;
  }
}
