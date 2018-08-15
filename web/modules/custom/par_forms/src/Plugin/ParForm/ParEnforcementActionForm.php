<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "enforcement_action",
 *   title = @Translation("Form for adding new enforcement actions.")
 * )
 */
class ParEnforcementActionForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['title', 'par_data_enforcement_action', 'title', NULL, NULL, 0, [
      'This value should not be null.' => 'You must enter a title for this enforcement action.'
    ]],
    ['details', 'par_data_enforcement_action', 'details', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the details of this enforcement action.'
    ]],
    ['field_regulatory_function', 'par_data_enforcement_action', 'field_regulatory_function', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must choose which regulatory functions this enforcement action relates to.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $reg_function_names = $par_data_partnership ? $par_data_partnership->getPartnershipRegulatoryFunctionNames() : [];

    $action_label = $this->getCardinality() !== 1 ?
      $this->formatPlural($cardinality, 'Details of Enforcement Action @index', 'Details of Enforcement Action @index (Optional)', ['@index' => $cardinality]) :
      $this->t('Details of Enforcement Action');

    $form['action'] = [
      '#type' => 'fieldset',
      '#title' => $action_label,
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the title'),
      '#default_value' => $this->getDefaultValuesByKey('title', $cardinality),
    ];

    $form['regulatory_function'] = [
      '#type' => 'radios',
      '#title' => $this->t('Choose a regulatory function to which this action relates'),
      '#options' => $reg_function_names,
      '#attributes' => ['class' => ['form-group']],
      '#default_value' => $this->getDefaultValuesByKey('regulatory_function', $cardinality),
    ];

    $form['details'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide details about this action'),
      '#default_value' => $this->getDefaultValuesByKey('details', $cardinality),
    ];

    $enforcement_action_fields = $this->getEntityFieldManager()->getFieldDefinitions('par_data_enforcement_action', 'document');
    $field_definition = $enforcement_action_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Add an attachment'),
      '#upload_location' => 's3private://documents/enforcement_action/',
      '#multiple' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return $form;
  }
}
