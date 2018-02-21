<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\FileInterface;
use Drupal\file\Entity\File;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;

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

    // Multiple file field.
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload a list of members'),
      '#description' => t('Upload your CSV file, be sure to make sure the'
        . ' information is accurate so that it can all be processed'),
      '#upload_location' => 's3private://member-csv/',
      '#multiple' => FALSE,
      '#required' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('csv'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => 'csv',
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Define array variable.
    $rows = [];

    // Process uploaded csv file.
    if ($csv = $this->getFlowDataHandler()->getTempDataValue('csv')) {
      // Load the submitted file and process the data.
      /** @var $files File[] * */
      $files = File::loadMultiple($csv);
      foreach ($files as $file) {
        // Validate file.
        $form_state->setError($form, 'The file you have uploaded is not in the right format.');
        try {
          $rows[] = $this->getCsvHandler()->loadFile($file, $rows);
        }
        catch (InvalidArgumentException $e) {
          $rows = [];
        }
//        dpm($rows);
      }
//
//      // Validate csv data.
//      foreach ($rows as $row => $data) {
//        $violations[$row] = $this->getCsvHandler()->validateRow($csv);
//      }
//
//      // Save the data in the User's temp private store for later processing.
//      if ($violations) {
//        $this->getFlowDataHandler()->setTempDataValue('errors', $violations);
//        $this->getFlowDataHandler()->setTempDataValue('coordinated_members', []);
//      }
//      else {
//        $this->getFlowDataHandler()->setTempDataValue('errors', []);
//        $this->getFlowDataHandler()->setTempDataValue('coordinated_members', $rows);
//      }
    }
  }

}
