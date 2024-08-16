<?php

namespace Drupal\par_data\Plugin\views\field;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_data_member_number")
 *
 * Field handler to get display the users
 * who are considered members of an entity.
 */
class ParDataMemberNumber extends FieldPluginBase {

  /**
   * Run the query.
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Render the result.
   */
  public function render(ResultRow $values) {
    $entity = $this->getEntity($values);

    return $entity instanceof ParDataPartnership && $entity->isCoordinated() ?
      $entity->numberOfMembers() :
      NULL;
  }

}
