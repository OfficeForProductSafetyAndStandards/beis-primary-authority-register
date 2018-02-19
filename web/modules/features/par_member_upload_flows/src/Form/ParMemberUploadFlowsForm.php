<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

//use Drupal\Core\Entity;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberUploadFlowsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_member_upload_csv';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

//    $entity_type = $this->getParDataManager()->getEntitiesByProperty('par_data_coordinated_business', 'field_organisation', 1);
//    dpm($entity_type);
//    $user = User::load(\Drupal::currentUser()->id());
//    $uid = $user->get('uid')->value;
//    dpm('uid: ' . $uid);
//    $entity_type = \Drupal::service('par_data.manager')->getParEntityType('par_data_coordinated_business');
//    $entity_type = $this->getParDataManager()->getEntitiesByProperty('par_data_coordinated_business', 'field_organisation', 1);
//    dpm($entity_type);
//
//    dpm($this->getFlowDataHandler()->getParameter('par_data_partnership')->id());
//
//    $csv_row_organisation_name = 'MY ORGANISATION NAME';
//    $csv_row_legal_entity_name = 'MY LEGAL ENTITY NAME';
//
//    $conditions = [
//      'matched' => [
//        'AND' => [
//          ['field_partnership', $this->getFlowDataHandler()->getParameter('par_data_partnership')->id(), 'IN'],
////          ['organisation_name', $csv_row_organisation_name, '='],
////          ['legal_entity_name', $csv_row_legal_entity_name, '='],
//        ],
//      ],
//    ];
//    $member = $this->getParDataManager()
//      ->getEntitiesByQuery('par_data_coordinated_business', $conditions, 1);
//    dpm($member->label());
//
//    dpm($user = \Drupal::currentUser());
//    dpm($user['id:protected']);
//    $user = User::load(\Drupal::currentUser()->id());
//    $uid = $user->get('uid')->value;
//    dpm($uid);
//    if ($entity instanceof \Drupal\Core\Entity\ContentEntityInterface) {
//      dpm('entity: ' . $entity);
//    }
//    dpm('ENTITY: ' . Entity::load($uid));
//    dpm('ENTITY: ' . Entity::get('field_coordinated_business')->getValue());
//    dpm(ParDataEntity::retrieveEntityIds('field_coordinated_business'));
//
    // Multiple file field.
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload a list of members'),
      '#description' => t('Upload your CSV file, be sure to make sure the'
        . ' information is accurate so that it can all be processed'),
      '#upload_location' => 's3private://member-csv/',
      '#multiple' => FALSE,
      '#required' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("csv"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => 'csv',
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Process uploaded csv file.
    if ($csv = $this->getFlowDataHandler()->getTempDataValue('csv')) {

      // Define array variable.
      $rows = [];

      // Load the submitted file and process the data.
      $files = File::loadMultiple($csv);
      foreach ($files as $file) {
        // Save processed row data in an array.
        $rows = $this->getParDataManager()->processCsvFile($file, $rows);
      }

      // Save the data in the User's temp private store for later processing.
      if (!empty($rows)) {

        $form_state->setValue('coordinated_members', $rows);
      }
    }
  }

}
