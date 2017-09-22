<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;

/**
 * The enforcement action document upload form.
 */
class ParEnforcementActionFileUploadForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_action_upload_file';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataEnforcementAction $enforcement_action = NULL) {

    $enforcement_action_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_enforcement_action', 'document');
    $field_definition = $enforcement_action_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload file(s)'),
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://documents/enforcement_action/',
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#default_value' => $this->getDefaultValues("files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }
}
