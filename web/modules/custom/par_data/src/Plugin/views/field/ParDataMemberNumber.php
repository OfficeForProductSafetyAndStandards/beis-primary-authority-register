<?php

namespace Drupal\par_data\Plugin\views\field;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get display the users who are considered members of an entity.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_data_member_number")
 */
class ParDataMemberNumber extends FieldPluginBase {

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

    return $entity instanceof ParDataPartnership && $entity->isCoordinated() ?
      $entity->numberOfMembers() :
      NULL;
  }

}
