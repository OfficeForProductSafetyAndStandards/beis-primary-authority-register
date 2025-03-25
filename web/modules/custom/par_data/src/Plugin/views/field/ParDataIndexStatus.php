<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\par_data\Plugin\views\field;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\TypedData\FieldItemDataDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\search_api\Plugin\views\field\SearchApiStandard;
use Drupal\search_api\SearchApiException;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get the PAR Data status on a search index.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_index_status")
 */
class ParDataIndexStatus extends SearchApiStandard {

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function render_item($count, $item) {
    $field = $this->getIndex()->getField($this->field);
    // Lookup the field definitions.
    $data_definition = $field->getDataDefinition();

    if ($data_definition instanceof FieldItemDataDefinition) {
      // Get the entity and field ids.
      $field_definition = $data_definition->getFieldDefinition();
      $bundle = $field_definition->getTargetBundle();
      $entity_field_name = $field_definition->getName();
      $entity_type_id = $field_definition->getTargetEntityTypeId();

      // Lookup the par data config for the field.
      $entity_type = $this->getParDataManager()->getParBundleEntity($entity_type_id, $bundle);

      // Render the allowed field label.
      return $entity_type->getAllowedFieldlabel($entity_field_name, $item['value']) ?? $item['value'];
    }

    $type = $this->definition['filter_type'] ?? 'plain';
    return $this->sanitizeValue($item['value'], $type);
  }
}
