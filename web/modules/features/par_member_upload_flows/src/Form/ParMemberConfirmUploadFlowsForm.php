<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

//use Drupal\file\Entity\File;

/**
 * The upload CSV confirmation form for importing partnerships.
 */
class ParMemberConfirmUploadFlowsForm extends ParBaseForm {

  // The base form controller for all PAR forms.
  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_member_upload_csv_confirm';
  }

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
          . ' Cancel link (below) and contact the Help Desk.') . '</p></b>',
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Form cache id.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');

    // Load temporary csv data and assign it to a variable.
    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    // Create CRON queue with a unique name.
    $queue = \Drupal::queue('par_member_upload');

    // @TODO Add each row to queue, or possibly to batch, or some way of saely handling 10,000 rows.

    parent::submitForm($form, $form_state);
  }

}
