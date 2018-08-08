<?php

namespace Drupal\par_forms;

use Drupal\par_data\ParDataManagerInterface;

/**
 * A simple mapping between form element and entity property.
 */
class ParEntityMapping {

  protected $fieldDefinition;
  protected $delta;
  protected $property;
  protected $elementKey;
  protected $errorMessageOverrides = [];

  /**
   * ParEntityMapping constructor.
   *
   * @param mixed $elementKey
   *   The element key. This can be a string if the element has no parents,
   *   or otherwise an array with all parents preceding the key.
   * @param string $entityType
   *   The entity type that the element value will be saved to.
   * @param string $field
   *   The entity field that the element value will be saved to.
   * @param null|string $property
   *   The field property that the element value will be saved to.
   * @param null|string $bundle
   *   The entity bundle that the element value will be saved to,
   *   only needed if not the default bundle.
   * @param int $delta
   *   The field delta that the element value will be saved to,
   *   only needed if the delta is relevant.
   * @param array $messageOverrides
   *   An array of message replacements where the key is the original
   *   entity violation message and the value is the replacement.
   */
  public function __construct($elementKey, string $entityType, string $field, string $property = NULL, string $bundle = NULL, int $delta = NULL, array $messageOverrides = []) {
    $this->fieldDefinition = $this->getParDataManager()->getFieldDefinition($entityType, $bundle, $field);

    $this->delta = $delta ?: 0;
    $this->property = $property;
    $this->elementKey = $elementKey;
    $this->errorMessageOverrides = $messageOverrides;
  }

  /**
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * Get the entity type for this mapping.
   *
   * @return string
   */
  public function getEntityType() {
    return $this->getParDataManager()->getParEntityType($this->getEntityTypeId());
  }

  /**
   * Get the entity type for this mapping.
   *
   * @return string
   */
  public function getEntityTypeId() {
    return $this->fieldDefinition->getTargetEntityTypeId();
  }

  /**
   * Get the entity bundle for this mapping.
   *
   * @return string
   */
  public function getEntityBundle() {
    return $this->fieldDefinition->getTargetBundle();
  }

  /**
   * Get the field name for this mapping.
   *
   * @return string
   */
  public function getFieldName() {
    return $this->fieldDefinition->getName();
  }

  /**
   * Get the field property for this mapping.
   *
   * @return string|NULL
   */
  public function getFieldProperty() {
    return $this->property;
  }

  /**
   * Get the form element key for this mapping.
   *
   * @return string
   */
  public function getElement() {
    return $this->elementKey;
  }

  /**
   * Get the form element key for this mapping.
   *
   * @return string
   */
  public function getErrorMessage($message) {
    return isset($this->errorMessageOverrides[(string) $message]) ? $this->errorMessageOverrides[(string) $message] : $message;
  }
}
