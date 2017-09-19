<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The advice document upload form.
 */
class ParPartnershipFlowsMemberUploadForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_member_upload';
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
    if (isset($par_data_partnership)) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      $files = $par_data_advice->get('csv')->referencedEntities();
      $ids = [];
      foreach ($files as $file) {
        $ids[] = $file->id();
      }
      $this->loadDataValue('files', $ids);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_advice);

    // Multiple file field.
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload file(s)'),
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://member-csv/',
      '#multiple' => FALSE,
      '#required' => TRUE,
      '#default_value' => $this->getDefaultValues("csv"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => ['csv'],
        ]
      ]
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#name' => 'next',
      '#value' => t('Upload'),
    ];

    // Go back to Advice Documents list.
    $previous_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the advice entity from the URL.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    if ($csv = $this->getTempDataValue('advice_type')) {
      var_dump($csv); die;
    }
  }

}
