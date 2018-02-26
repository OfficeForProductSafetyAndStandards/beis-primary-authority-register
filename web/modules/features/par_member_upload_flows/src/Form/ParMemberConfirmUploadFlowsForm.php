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
    dpm($csv_data);

    // Use Batch API to process csv data.
//    $batch = [
//      'title' => t('Processing csv data'),
//      'operations' => [],
//      'finished' => 'par_member_upload_flows_batch_finished',
//    ];
//    // Batch set.
//    batch_set($batch);
//
    // Get all 'Business Name' and 'Legal Entity' for current business in an
    // array and use it to match with the csv rows below for processing (ADD
    // / UPDATE).
    //
    // Loop through the csv data and process.
//    for ($i = 0; $i <= 1; $i++) {
//      $batch['operations'][] = ['par_member_upload_flows_batch_process_item', []];
//      $entity_type = $this->getParDataManager()->getEntitiesByProperty('par_data_coordinated_business', 'field_organisation', $i);
//      // 25 seconds approx.
//      // Save each row in BATCH'S TEMP DATA.
//      // Update data.
//      // When BATCH FINISHES, match data from BATCH'S TEMP DATA with the
//      // MEMBER'S DATA and DELETE ids which are old data.
//      // Batch finishes.
//    }
    // Partnership object.
//    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
//    // @TODO lock the partnership.
//    par_member_upload_flows_lock_partnership($partnership);
//    // @TODO delete/remove members that are not in the newly uploaded CSV.
//    // @TODO add/update all new and updated records.
//    par_member_upload_flows_process_members();
//    // @TODO unlock the partnership after completion.
//    par_member_upload_flows_unlock_partnership($partnership);
//    // @TODO send an e-mail summary on completion.
//    par_member_upload_flows_send_email();
  }

}
