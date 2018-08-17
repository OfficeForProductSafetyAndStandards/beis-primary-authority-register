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
 * Field handler to get the PAR Data status time.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_data_status_time")
 */
class ParDataStatusTime extends FieldPluginBase {

  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $entity = $this->getEntity($values);

    if ($entity instanceof ParDataEntityInterface) {
      $time = $entity->getStatusTime($entity->getRawStatus());

      return $time ? $this->getDateFormatter()->format($time, 'gds_date_format') : '';
    }
  }
}
