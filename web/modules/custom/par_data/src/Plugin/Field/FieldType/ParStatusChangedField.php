<?php

namespace Drupal\par_data\Plugin\Field\FieldType;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Provides a field type that shows when the status was last updated.
 *
 * @FieldType(
 *   id = "par_status_changed_field",
 *   label = @Translation("PAR Status Changed"),
 *   default_widget = "datetime_timestamp",
 *   default_formatter = "timestamp",
 * )
 */
class ParStatusChangedField extends FieldItemList {

  use ComputedItemListTrait;

  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
  * {@inheritdoc}
  */
  #[\Override]
  protected function computeValue() {
    $entity = $this->getEntity();

    if ($entity instanceof ParDataEntityInterface) {
      $timestamp = $entity->getStatusTime($entity->getRawStatus());
      $datetime = $timestamp ? DrupalDateTime::createFromTimestamp($timestamp) : NULL;

      $this->list[] = $this->createItem(0, $datetime?->format('Y-m-d'));
    }
  }

}
