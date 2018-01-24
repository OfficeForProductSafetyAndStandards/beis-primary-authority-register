<?php

namespace Drupal\par_data\Views;

use Drupal\par_data\Entity\ParDataTypeInterface;
use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for trance entities.
 */
class ParDataViewsData extends EntityViewsData implements EntityViewsDataInterface {

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

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

    // Document Completion as a %.
    $data['par_partnerships_revision']['document_completion'] = array(
      'title' => t('Documents Completion Percentage'),
      'field' => [
        'title' => t('Documents Completion Percentage'),
        'help' => t('Completion percentage for partnership required documents'),
        'id' => 'par_partnership_revision_documents_completion_percentage',
      ],
    );

    // PAR Status Filter.
    // This will always get the allowed statuses from the first bundle.
    // @TODO Get the list of bundles which the view supports and use these.
    $par_entity = $this->getParDataManager()->getParEntityType($this->entityType->id());
    $entity_bundle = $this->getParDataManager()->getParBundleEntity($this->entityType->id());
    if (isset($par_entity) && isset($entity_bundle) && $entity_bundle instanceof ParDataTypeInterface) {
      $status_field = $entity_bundle->getConfigurationElementByType('entity', 'status_field');
    }
    if (isset($status_field)) {
      $data[$this->entityType->getDataTable()][$status_field]['filter'] = [
        'title' => t('PAR Status'),
        'help' => t('Provides a status filter with configurable values to include.'),
        'id' => 'par_data_status_filter',
      ];
    }

    // PAR Status Field.
    $data[$this->entityType->getDataTable()]['par_status'] = [
      'title' => t('PAR Status'),
      'field' => [
        'title' => t('PAR Status'),
        'help' => t('Provides the status field for PAR entities.'),
        'id' => 'par_data_status',
      ],
    ];

    // PAR Flow Link.
    $data[$this->entityType->getDataTable()]['par_flow_link'] = [
      'title' => t('PAR Flow Link'),
      'field' => [
        'title' => t('PAR Flow Link'),
        'help' => t('Provides a link with the entity title.'),
        'id' => 'par_flow_link',
      ],
    ];

    // PAR Partnership Flow Link.
    $data[$this->entityType->getDataTable()]['par_partnership_flow_link'] = [
      'title' => t('PAR Partnership Flow Link'),
      'field' => [
        'title' => t('PAR Partnership Flow Link'),
        'help' => t('Provides a link to the relevant partnership journey determined by the partnership status.'),
        'id' => 'par_partnership_flow_link',
      ],
    ];

    // Custom filter for Par Membership checks.
    $data[$this->entityType->getDataTable()]['id_filter'] = [
      'title' => t('Membership Filter'),
      'filter' => [
        'title' => t('Membership Filter'),
        'help' => t('Filter by entities this user can update.'),
        'id' => 'par_member',
      ],
    ];

    return $data;
  }

}
