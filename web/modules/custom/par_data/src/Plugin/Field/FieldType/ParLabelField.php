<?php

namespace Drupal\par_data\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Provides a field type of baz.
 *
 * @FieldType(
 *   id = "par_label_field",
 *   label = @Translation("PAR Label"),
 *   default_formatter = "string",
 *   default_widget = "string_textfield",
 * )
 */
class ParLabelField extends FieldItemList {

  use ComputedItemListTrait;

  /**
  * {@inheritdoc}
  */
  protected function computeValue() {
    return $this->getEntity()->label();
  }

}
