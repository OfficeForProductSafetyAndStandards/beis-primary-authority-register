<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParEnforcementAddActionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_add_action';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues();

    $reg_function_names = $par_data_partnership->getPartnershipRegulatoryFunctionNames();

    $form['title_of_action'] = [
      '#title' => $this->t('Title of action'),
      '#type' => 'textfield',
      '#default_value' => $this->getDefaultValues('title_of_action'),
    ];

    $form['regulatory_functions'] = [
      '#type' => 'radios',
      '#title' => $this->t('Regulatory function to which this relates'),
      '#options' => $reg_function_names,
      '#default_value' => $this->getDefaultValues('regulatory_functions'),
      '#required' => TRUE,
    ];

    $form['details'] = [
      '#title' => $this->t('Details'),
      '#type' => 'textarea',
      '#default_value' => $this->getDefaultValues('details'),
    ];

    $enforcement_action_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_enforcement_action', 'document');
    $field_definition = $enforcement_action_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Attach file(s)'),
      '#description' => t('(document or photo)'),
      '#upload_location' => 's3private://documents/enforcement_action/',
      '#multiple' => TRUE,
      '#default_value' => $this->getDefaultValues("files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#name' => 'next',
      '#value' => $this->t('Continue'),
    ];

    $cancel_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
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

    $title = $this->getTempDataValue('title_of_action');

    $enforcementAction_data = [
      'type' => 'enforcement_action',
      'title' => $title,
      'details' => $this->getTempDataValue('details'),
      'document' => $this->getDefaultValues("files"),
      'field_regulatory_function' => $this->getTempDataValue('regulatory_functions'),
    ];

    $enforcementAction = \Drupal::entityManager()->getStorage('par_data_enforcement_action')->create($enforcementAction_data);

    if ($enforcementAction->save()) {

      $enforcement_notice = $this->getRouteParam('par_data_enforcement_notice');
      //Store the created action on the current enforcement entity.
      $enforcement_action_ids = $enforcementAction->id();

      $enforcement_notice->field_enforcement_action[] = $enforcement_action_ids;
      $enforcement_notice->save();
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %action_entity could not be saved for %form_id');
      $replacements = [
        '%action_entity' => $title,
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
