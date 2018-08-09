<?php

namespace Drupal\par_forms;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolationInterface;

trait ParEntityValidationMappingTrait {

  /**
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * @return Serializer
   */
  public function getSerializer() {
    return \Drupal::service('serializer');
  }

  /**
   * @return ParEntityMapping[]
   */
  public function getEntityMappings() {
    $map = property_exists($this, 'entityMapping') ? $this->entityMapping : [];
    $mappings = [];

    foreach ($map as $mapping) {
      try {
        $mappings[] = new ParEntityMapping(...$mapping);
      }
      catch(\TypeError $e) {
        $this->getLogger($this->getLoggerChannel())->critical('An error occurred trying to create entity validation mapping for: @mapping `@details`', ['@mapping' => $this->getSerializer()->serialize($mapping, 'json'), '@details' => $e->getMessage()]);
      }
    }
    
    return $mappings;
  }

  /**
   * @return ParEntityMapping[]
   */
  public function getMappingByEntityType($entityType) {
    return array_filter($this->getEntityMappings(), function ($mapping) use ($entityType) {
      return ($mapping->getEntityTypeId() === $entityType);
    });
  }

  /**
   * @return ParEntityMapping[]
   */
  public function getFieldNamesByEntityType($entityType) {
    $field_names = [];

    foreach ($this->getMappingByEntityType($entityType) as $mapping) {
      $field_names[] = $mapping->getFieldName();
    }

    return $field_names;
  }

  /**
   * A helper function to get the element based on the entity path.
   *
   * @param ConstraintViolationInterface $violation
   *
   * @return ParEntityMapping
   *   The map that relates to this violation.
   */
  public function getElementByViolation($violation) {
    $matched = array_filter($this->getEntityMappings(), function ($mapping) use ($violation) {
      /** @var $mapping ParEntityMapping */
      if ($violation->getRoot() instanceof EntityAdapter) {
        @list($field_name, $delta, $property) = explode('.', $violation->getPropertyPath(), 3);
      }
      elseif ($violation->getRoot() instanceof FieldItemListInterface) {
        $field_name = $violation->getRoot()->getName();
        @list($delta, $property) = explode('.', $violation->getPropertyPath(), 3);
      }

      return (isset($field_name) && $mapping->getFieldName() === $field_name
        && (!$mapping->getFieldProperty() || (isset($property) && $mapping->getFieldProperty() === $property)));
    });

    // Return the first matched mapping.
    return !empty($matched) ? current($matched) : NULL;
  }

  /**
   * Get the error message from a violation and wrap it accordingly.
   *
   * @param ConstraintViolationInterface $violation
   *   The violation to display.
   * @param ParEntityMapping $mapping
   *   The mapping that handles the display of this error.
   * @param $id
   *   The form element id to link to.
   *
   * @return \Drupal\Core\GeneratedLink
   */
  public function getViolationMessage(ConstraintViolationInterface $violation, ParEntityMapping $mapping, $id) {
    $message = $mapping->getErrorMessage($violation->getMessage());
    return $this->wrapErrorMessage($message, $id);
  }

  /**
   * This function isn't exclusive to entity validation but we can keep it here for now.
   *
   * @param $message
   *   The message to wrap.
   * @param $id
   *   The form id to link it to.
   *
   * @return \Drupal\Core\GeneratedLink
   */
  public function wrapErrorMessage($message, $id) {
    $url = Url::fromUri('internal:#', ['fragment' => $id]);
    $link = Link::fromTextAndUrl($message, $url)->toString();
    return $link;
  }

  /**
   * A helper function to build the entities.
   */
  public function buildEntity(&$entity, $values) {
    $fields = [];
    foreach ($this->getMappingByEntityType($entity->getEntityTypeId()) as $mapping) {
      $value = NestedArray::getValue($values, $this->getElementKey($mapping->getElement()));

      if ($mapping->getFieldProperty()) {
        if (!isset($fields[$mapping->getFieldName()])) {
          $fields[$mapping->getFieldName()] = [];
        }

        $fields[$mapping->getFieldName()][$mapping->getFieldProperty()] = $value;
      }
      else {
        $fields[$mapping->getFieldName()] = $value;
      }
    }

    foreach ($fields as $field_name => $field_value) {
      if ($entity->hasField($field_name)) {
        $entity->set($field_name, $field_value);
      }
    }
  }

  public function createMappedEntities() {
    $entities = [];

    // Remove any universally banned relationships.
    foreach ($this->getEntityMappings() as $mapping) {
      if ($mapping instanceof ParEntityMapping && !isset($entities[$mapping->getEntityTypeId() . ':' . $mapping->getEntityBundle()])) {
        $entities[$mapping->getEntityTypeId() . ':' . $mapping->getEntityBundle()] = $this->createMappedEntity($mapping->getEntityTypeId(), $mapping->getEntityBundle());
      }
    }

    return $entities;
  }

  public function createMappedEntity($type, $bundle) {
    $entity_class = $this->getParDataManager()->getParEntityType($type)->getClass();
    return $entity_class::create([
      'type' => $bundle,
    ]);
  }

  /**
   * Get's the element key depending on the cardinality of this plugin.
   *
   * @param $element
   *   The element key.
   * @param int $cardinality
   *   The cardinality of this element.
   *
   * @return string|array
   *   The key for this form element.
   */
  public function getElementKey($element) {
    return (array) $element;
  }

  /**
   * Get's the element name depending on the cardinality of this plugin.
   *
   * @param $element
   *   The element key.
   *
   * @return string
   *   The key for this form element.
   */
  public function getElementName($element) {
    $name = implode('][', (array) $element);
    if (count((array) $element) > 1) {
      $name .= ']';
    }

    return $name;
  }

  /**
   * Get's the element ID depending on the cardinality of this plugin.
   *
   * @param $element
   *   The element key.
   *
   * @return string
   *   The key for this form element.
   */
  public function getElementId($element, $form) {
    $element = (array) $element;
    array_push($element, '#id');
    $id = NestedArray::getValue($form, $element);

    return $id;
  }

}
