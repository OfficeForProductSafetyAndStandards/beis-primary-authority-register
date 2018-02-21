<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;

/**
 * The upload CSV confirmation form for importing partnerships.
 */
class ParMemberConfirmUploadFlowsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Load csv data from temporary data storage and assign to a variable.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    var_dump($csv_data);

    // Upload csv file confirmation message.
    $form['csv_upload_confirmation_message_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Are you sure you want to upload a new list of'
        . ' members?'),
      'intro' => [
        '#type' => 'markup',
        '#markup' => '<p><b>' . $this->t('This operation will erase any'
          . ' existing list of members. If you are unsure, please click the'
          . ' Cancel link (below) and contact the Help Desk.') . '</b></p>',
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Load csv data from temporary data storage and assign to a variable.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    var_dump($csv_data);

    // Initialise default value.
    $row_number = 0;
    $error_message = [];

    // Loop through all csv rows and process data.
    foreach ($csv_data[0] as $key => $value) {

      // Set row number.
      $row_number = $key + 2;

      // CSV data validation - File format OK, all columns present, but
      // missing required field(s).
      if (!par_member_upload_flows_required_fields($value, $row_number) == NULL) {
        $error_message[] = par_member_upload_flows_required_fields($value, $row_number);
      }
    }

    // If there is an error message, then process following.
    if (count($error_message) > 0) {

      // Prepare error message variable.
      $error = 'We found the following errors: <br><br>';
      $error .= implode('<br>', $error_message);
      $error .= '<br><br>Please check and try again.';

      // Display error to the end user.
      drupal_set_message(t('@error', ['@error' => $error]), 'error');
//      drupal_set_message(t('@error', ['@error' => nl2br($error)]), 'error');
//      drupal_set_message(nl2br($error), 'error'); //
//      drupal_set_message($error);//
      dpm($error);
    }
  }

}
