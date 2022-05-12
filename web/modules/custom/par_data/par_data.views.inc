<?php

/**
 * @file
 * The views data file.
 */

use \Drupal\search_api\Entity\Index;

/**
 * Implements hook_views_data_alter().
 */
function par_data_views_data_alter(array &$data) {
  // Check that search index has been installed.
  $moduleHandler = \Drupal::service('module_handler');
  if ($moduleHandler->moduleExists('search_api')) {
    foreach (Index::loadMultiple() as $index) {
      $index_table = 'search_api_index_' . $index->id();

      // This is a global field, not related to any particular entities and as
      // such should be added to the index base table.
      $data[$index_table]['par_flow_link'] = [
        'title' => t('PAR Flow Link'),
        'field' => [
          'title' => t('PAR Flow Link'),
          'help' => t('Provides a link with the entity title.'),
          'id' => 'par_flow_link',
        ],
      ];
    }
  }
}
