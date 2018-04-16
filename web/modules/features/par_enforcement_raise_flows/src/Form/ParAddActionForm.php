<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParAddActionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add an action to the  enforcement notice';

  /**
   * {@inheritdoc}
   */
  protected $formItems = [
    'par_data_enforcement_action:enforcement_action' => [
      'title' => 'title',
      'details' => 'details',
      'field_regulatory_function' => 'field_regulatory_function'
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $reg_function_names = $par_data_partnership->getPartnershipRegulatoryFunctionNames();

    $form['title_of_action_title'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enter the title of action'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('title'),
    ];

    $form['field_regulatory_function'] = [
      '#type' => 'radios',
      '#title' => $this->t('Choose a regulatory function to which this action relates'),
      '#options' => $reg_function_names,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('field_regulatory_function'),
    ];

    $form['details_title'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Provide details about this action'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
    ];

    $form['details'] = [
      '#type' => 'textarea',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('details'),
    ];

    $enforcement_action_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_enforcement_action', 'document');
    $field_definition = $enforcement_action_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    $form['files_title'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add an attachment'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
    ];

    // Multiple file field.
    $form['files_title']['files'] = [
      '#type' => 'managed_file',
      '#upload_location' => 's3private://documents/enforcement_action/',
      '#multiple' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("files"),
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

    $title = $this->getFlowDataHandler()->getTempDataValue('title');

    $enforcementAction_data = [
      'type' => 'enforcement_action',
      'title' => $title,
      'details' => $this->getFlowDataHandler()->getTempDataValue('details'),
      'document' => $this->getFlowDataHandler()->getDefaultValues("files"),
      'field_regulatory_function' => $this->getFlowDataHandler()->getTempDataValue('field_regulatory_function'),
    ];

    $enforcementAction = \Drupal::entityManager()->getStorage('par_data_enforcement_action')->create($enforcementAction_data);

    if ($enforcementAction->save()) {

      $enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
      // Store the created action on the current enforcement entity.
      $enforcement_action_ids = $enforcementAction->id();

      $enforcement_notice->field_enforcement_action[] = $enforcement_action_ids;
      $enforcement_notice->save();
      $this->getFlowDataHandler()->deleteStore();
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
