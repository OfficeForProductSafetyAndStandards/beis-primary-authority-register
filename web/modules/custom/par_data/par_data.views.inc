<?php

/**
 * Implements hook_views_data_alter().
 */
function par_data_views_data_alter(array &$data) {

  $data['par_partnerships_revision']['document_completion'] = array(
    'title' => t('Documents Completion Percentage'),
    'field' => [
      'title' => t('Documents Completion Percentage'),
      'help' => t('Completion percentage for partnership required documents'),
      'id' => 'par_partnership_revision_documents_completion_percentage',
    ],
  );

  $data['par_partnerships_revision']['par_combined_status_field'] = array(
    'title' => t('Combined Status Field'),
    'field' => [
      'title' => t('Combined Status Field'),
      'help' => t('Provides a status field that combines several field statuses'),
      'id' => 'par_partnerships_combined_status_field',
    ],
  );

}
