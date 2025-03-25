<?php

namespace Drupal\par_data\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Provides a field type that displays the number of members on a partnership.
 *
 * @FieldType(
 *   id = "par_members_field",
 *   label = @Translation("PAR Members"),
 *   default_formatter = "number_integer",
 *   default_widget = "number",
 * )
 */
class ParMembersField extends FieldItemList {

  use ComputedItemListTrait;

  /**
  * {@inheritdoc}
  */
  #[\Override]
  protected function computeValue() {
    $entity = $this->getEntity();

    if ($entity instanceof ParDataPartnership
      && $entity->isCoordinated()) {
      $this->list[] = $this->createItem(0, $entity->numberOfMembers());
    }
  }

}
