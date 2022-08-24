<?php

/**
 * @file
 * The views data file.
 */

use \Drupal\search_api\Entity\Index;
use \Drupal\Core\Field\TypedData\FieldItemDataDefinition;

/**
 * Implements hook_views_data_alter().
 */
function par_data_views_data_alter(array &$data) {
  // Check that search index has been installed.
  $moduleHandler = \Drupal::service('module_handler');
  if (!$moduleHandler->moduleExists('search_api')) {
    return;
  }

  // Get the par data manager.
  $par_data_manager = \Drupal::service('par_data.manager');

  foreach (Index::loadMultiple() as $index) {
    $index_table = 'search_api_index_' . $index->id();
    $table = &$data[$index_table];

    // Add the flow link to the global base table.
    $data[$index_table]['par_flow_link'] = [
      'title' => t('PAR Flow Link'),
      'field' => [
        'title' => t('PAR Flow Link'),
        'help' => t('Provides a link with the entity title.'),
        'id' => 'par_flow_link',
      ],
    ];

    // Add suitable handlers for all indexed fields.
    foreach ($index->getFields(TRUE) as $field_id => $field) {
      // Lookup the field definitions.
      $data_definition = $field->getDataDefinition();

      if ($data_definition instanceof FieldItemDataDefinition) {
        // Get the entity and field ids.
        $field_definition = $data_definition->getFieldDefinition();
        $bundle = $field_definition->getTargetBundle();
        $field_name = $field_definition->getName();
        $entity_type_id = $field_definition->getTargetEntityTypeId();

        // Lookup the par data config for the field.
        $entity_type = $par_data_manager->getParBundleEntity($entity_type_id, $bundle);

        // Check whether this field has allowed values.
        $allowed_values = $entity_type->getConfigurationElementByType($field_name, 'allowed_values');

        // Modify the default views handler for par allowed_value filters.
        if (!empty($allowed_values)) {
          $mapping = _search_api_views_handler_mapping();
          $table[$field_id]['filter']['id'] = $mapping['options']['filter']['id'];
          $table[$field_id]['filter']['options callback'] = '_par_data_search_api_views_get_allowed_values';
          $table[$field_id]['filter']['options arguments'] = [$entity_type_id, $bundle, $field_name];
        }

        // Modify the default views handler for entity reference lists.
        if ($field_definition->getType() === 'entity_reference'
            && $par_data_manager->getParEntityType($field_definition->getsetting('target_type'))) {
          $target_type = $field_definition->getsetting('target_type');

          $count = $par_data_manager->getEntityQuery($target_type)->count()->execute();

          // A list can be displayed if less than 20.
          if ($count <= 20) {
            $mapping = _search_api_views_handler_mapping();
            $table[$field_id]['filter']['id'] = $mapping['options']['filter']['id'];
            $table[$field_id]['filter']['options callback'] = '_par_data_search_api_views_get_entity_values';
            $table[$field_id]['filter']['options arguments'] = [$target_type];
          }
          // Otherwise an autocomplete can be added.
          else {
            // @TODO Create a new autocomplete filter.
          }
        }
      }
    }

  }
}
