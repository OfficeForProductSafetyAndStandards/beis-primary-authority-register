<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberUploadFlowsForm extends ParBaseForm {

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
