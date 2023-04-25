<?php

namespace Drupal\par_data\Plugin\Field\FieldType;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Provides a field type that displays when the number of members was last updated.
 *
 * @FieldType(
 *   id = "par_members_list_updated_field",
 *   label = @Translation("PAR Members List Updated"),
 *   default_widget = "datetime_timestamp",
 *   default_formatter = "timestamp",
 * )
 */
class ParMemberListUpdatedField extends FieldItemList {

  use ComputedItemListTrait;

  /**
  * {@inheritdoc}
   */
  protected function computeValue() {
    $entity = $this->getEntity();

    if ($entity instanceof ParDataPartnership && $entity->isCoordinated()) {
      $timestamp = $entity->membersLastUpdated();
      $datetime = $timestamp ? DrupalDateTime::createFromTimestamp($timestamp) : NULL;

      $this->list[] = $this->createItem(0, $datetime?->format('Y-m-d'));
    }
  }

}
