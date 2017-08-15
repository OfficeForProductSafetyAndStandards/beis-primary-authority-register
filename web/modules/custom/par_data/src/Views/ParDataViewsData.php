<?php

namespace Drupal\par_data\Views;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for trance entities.
 */
class ParDataViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $entity_type_label = $this->entityType->getLabel();

    if (isset($data[$this->entityType->getDataTable()]['table']['base']['title'])) {
      $data[$this->entityType->getDataTable()]['table']['base']['title'] = $entity_type_label;
    }
    if (isset($data[$this->entityType->getRevisionDataTable()]['table']['base']['title'])) {
      $data[$this->entityType->getRevisionDataTable()]['table']['base']['title'] = $this->t('@label revision', [
        '@label' => $entity_type_label,
      ]);
    }

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

    return $data;
  }

}
