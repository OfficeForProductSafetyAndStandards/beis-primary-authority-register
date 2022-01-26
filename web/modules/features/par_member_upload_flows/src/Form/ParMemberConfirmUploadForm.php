<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandler;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;

/**
 * The upload CSV confirmation form for importing partnerships.
 */
class ParMemberConfirmUploadForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm member upload';

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Upload csv file confirmation message.
    $form['csv_upload_confirmation_message_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Are you sure you want to upload a new list of members?'),
      'intro' => [
        '#type' => 'markup',
        '#markup' => '<p><b>' . $this->t('This operation will erase any existing list of members. If you are unsure, please click the Cancel link (below) and contact the Help Desk.') . '</b></p>',
      ],
      'warning' => [
        '#type' => 'markup',
        '#markup' => '<p><b>' . $this->t('Please do not press the back button, close the browser or leave this page while the member list is uploading.') . '</b></p>',
      ],
    ];

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Upload');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($par_data_partnership->isMembershipLocked()) {
      $form_state->setError($form, 'The membership is currently locked and cannot be processed right now.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Load csv data from temporary data storage and assign to a variable.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Process the data in one go if less than half the maximum batch size.
    if (!empty($csv_data) && count($csv_data) <= (ParMemberCsvHandler::BATCH_LIMIT/2)) {
      $uploaded = $this->getCsvHandler()->upload($csv_data, $par_data_partnership);
    }
    else if (!empty($csv_data)) {
      $uploaded = $this->getCsvHandler()->batchUpload($csv_data, $par_data_partnership);
    }

    // Log the result.
    if ($uploaded) {
      $this->getFlowDataHandler()->deleteStore();
    } else {
      $message = $this->t('Membership list could not be processed for %form_id');
      $replacements = [
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
