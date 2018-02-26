<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
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
    $violations = [];

    // Process uploaded csv file.
    if ($csv = $this->getFlowDataHandler()->getTempDataValue('csv')) {
      // Load the submitted file and process the data.
      /** @var $files File[] * */
      $files = File::loadMultiple($csv);

      // Loop through each csv row from uploaded file and save in $row array.
      foreach ($files as $file) {
        $rows = $this->getCsvHandler()->loadFile($file, $rows);
      }

      // Check if uploaded csv file's column numbers are correct.
      if (count($rows[0]) <> 24) {
        $violations[] = 'CSV File - Column numbers must be 24.';
      }

      // Loop through each column of csv row and check if any required column
      // has missing data. If yes then add error message in an array.
      if (count($rows[0]) == 24) {
        foreach ($rows as $row => $data) {
          if (count($this->getCsvHandler()->validateRow($data, $row)) > 0) {
            $violations[$row + 2] = $this->getCsvHandler()->validateRow($data, $row);
          }
        }
      }

      // If csv has missing data in any of the required fields.
      if (count($violations) > 0) {
        $form_state->setValue('errors', $violations);
        $form_state->setValue('coordinated_members', []);
      }

      // If csv has no missing data in any of the required fields.
      else {
        $form_state->setValue('errors', []);
        $form_state->setValue('coordinated_members', $rows);
      }
    }
  }

}
