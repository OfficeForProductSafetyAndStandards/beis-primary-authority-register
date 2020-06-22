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
      $data[$this->entityType->getDataTable()][$status_field]  = [
        'title' => t('PAR Status Field'),
        'filter' => [
          'title' => t('PAR Status'),
          'help' => t('Provides a status filter with configurable values to include.'),
          'id' => 'par_data_status_filter',
        ],
        'sort' => [
          'title' => t('PAR Status'),
          'help' => t('Provides a sort filter on the status field for PAR entities.'),
          'id' => 'par_sort_allowed_statuses',
        ],
      ];
    }

    // PAR Status Field.
    $data[$this->entityType->getDataTable()]['par_label'] = [
      'title' => t('PAR Label'),
      'field' => [
        'title' => t('PAR Label'),
        'help' => t('Provides the generated label for PAR entities.'),
        'id' => 'par_data_label',
      ],
    ];

    // PAR Status Field.
    $data[$this->entityType->getDataTable()]['par_status'] = [
      'title' => t('PAR Status'),
      'field' => [
        'title' => t('PAR Status'),
        'help' => t('Provides the status field for PAR entities.'),
        'id' => 'par_data_status',
      ],
    ];

    // PAR Status Updated Field.
    $data[$this->entityType->getDataTable()]['par_status_time'] = [
      'title' => t('PAR Status Time'),
      'field' => [
        'title' => t('PAR Status Time'),
        'help' => t('Provides the time the status of a PAR entity was last updated.'),
        'id' => 'par_data_status_time',
      ],
    ];

    // PAR Status Author Field.
    $data[$this->entityType->getDataTable()]['par_status_author'] = [
      'title' => t('PAR Status Author'),
      'field' => [
        'title' => t('PAR Status Author'),
        'help' => t('Provides the author of the last status change.'),
        'id' => 'par_data_status_author',
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
    // @deprecated
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

    // Custom filter for grouping people by their user account emails.
    if ($this->entityType->id() === 'par_data_person') {
      $data[$this->entityType->getDataTable()]['par_person_email'] = [
        'title' => t('Person Account Email'),
        'field' => [
          'title' => t('Person Account Email'),
          'help' => t('Displays the email address for this persons account over the email address for the person.'),
          'id' => 'par_person_account_email',
        ],
      ];
    }

    return $data;
  }

}
