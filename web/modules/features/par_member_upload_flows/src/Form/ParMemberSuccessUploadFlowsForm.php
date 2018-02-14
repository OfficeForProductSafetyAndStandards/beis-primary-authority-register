<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

//use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
//use Drupal\file\Entity\File;

/**
 * The upload CSV success page for importing partnerships.
 */
class ParMemberSuccessUploadFlowsForm extends ParBaseForm {
  // The base form controller for all PAR forms.
//  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_member_upload_csv_success';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Upload csv file success message.
    $form['csv_upload_success_message_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What happens next?'),
      'intro' => [
        '#type' => 'markup',
        '#markup' => '<p>' . $this->t('Your member list will be processed'
          . ' shortly, you will receive an email notification when this is'
          . ' complete.<br /><br />'
          . 'Please allow 10 minutes for members to appear. If there is an'
          . ' error with the processing, the email will guide you on where'
          . ' your files need amending.') . '</p>',
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

//    // Form cache id.
//    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
//
//    // Load temporary csv data and assign it to a variable.
//    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);
//
//    // Create CRON queue with a unique name.
//    $queue = \Drupal::queue('par_member_upload');
//
//    // Create CRON QUEUE item which is added to the queue and will be
//    // triggered next time when CRON executes.
//    $queue->createItem($csv_data);

    parent::submitForm($form, $form_state);
  }

}
