<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;

/**
 * The advice document upload form for Transition Journey 1.
 */
class ParFlowTransitionAdviceUploadForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_partnership_advice_document_upload';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataAdvice $par_data_advice
   *   The advice being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    if (isset($par_data_advice)) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_advice->id()}");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {

//    $this->retrieveEditableValues($par_data_partnership);

    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Files:'),
      '#upload_location' => 's3private://documents/advice/',
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#default_value' => $this->getDefaultValues("files")
    ];

    $form['good'] = [
      '#type' => 'textarea',
      '#title' => t('Good:'),
      '#default_value' => $this->getDefaultValues("good")
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    // Make sure to add the document cacheability data to this form.
//    $this->addCacheableDependency($par_data_advice);
//    $this->addCacheableDependency($advice_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);

//    $errors = $par_data_advice->validateFields($fields);
    // Display error messages.
//    foreach($errors as $field => $message) {
//      $form_state->setErrorByName($field, $message);
//    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

//    // Get files from temp store.
//    $files = $this->getTempDataValue('files');
//
//    // Loop through files, save as permanent storage.
//    foreach($files as $file) {
//
//      $file = File::load($file);
//      $file->setPermanent();
//      $file->save();
//
//      $files_to_add[]['target_id'] = $file->id();
//
//    }
//
//    // Prepare Advice entity.
//    $advice = \Drupal\par_data\Entity\ParDataAdvice::create([
//      'type' => 'advice',
//      'uid' => 1,
//      'document' => $files_to_add,
//    ]);
//
//    $advice->save();

    // Go to the "document add" screen.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(11), $this->getRouteParams());
  }

}
