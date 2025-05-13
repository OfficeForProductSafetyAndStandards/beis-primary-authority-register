<?php

namespace Drupal\par_data\Plugin\views\field;

use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get the PAR Data status.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_data_status")
 */
class ParDataStatus extends FieldPluginBase {

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

    if ($entity instanceof ParDataEntityInterface) {
      $status = $entity->getParStatus();

      return $status ? t($status) : '';
    }
  }

}
