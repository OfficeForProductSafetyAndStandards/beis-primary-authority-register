<?php

namespace Drupal\par_data\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;

/**
 * The PAR Status field will display the current status of a PAR Data entity.
 *
 * @FieldType(
 *   id = "par_status",
 *   label = @Translation("PAR Status"),
 *   description = @Translation("The par status rendered as an entity."),
 *   default_widget = "string_textfield",
 *   default_formatter = "par_list_formatter"
 * )
 */
class ParStatus extends StringItem {

  /**
   * Whether or not the value has been calculated.
   *
   * @var bool
   */
  protected $isCalculated = FALSE;

  /**
   * {@inheritdoc}
   */
  public function __get($name) {
    $this->ensureCalculated();
    return parent::__get($name);
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $this->ensureCalculated();
    return parent::isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    $this->ensureCalculated();
    return parent::getValue();
  }

  /**
   * Calculates the value of the field and sets it.
   */
  protected function ensureCalculated() {
    if (!$this->isCalculated) {
      $entity = $this->getEntity();
      $value = [
        'value' => $entity->getParStatus(),
      ];
      $this->setValue($value);

      $this->isCalculated = TRUE;
    }
  }

}
