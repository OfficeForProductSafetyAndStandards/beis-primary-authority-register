<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormBuilder;
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
  protected array $entityMapping = [
    ['title', 'par_data_enforcement_action', 'title', NULL, NULL, 0, [
      'This value should not be null.' => 'You must enter a title for this enforcement action.'
    ]],
    ['details', 'par_data_enforcement_action', 'details', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the details of this enforcement action.'
    ]],
    ['regulatory_function', 'par_data_enforcement_action', 'field_regulatory_function', NULL, NULL, 0, [
      'This value should be of the correct primitive type.' => 'You must choose which regulatory functions this enforcement action relates to.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $reg_function_names = $par_data_partnership ? $par_data_partnership->getPartnershipRegulatoryFunctionNames() : [];

    $action_label = $this->getCardinality() !== 1 ?
      $this->formatPlural($index, 'Details of Enforcement Action @index', 'Details of Enforcement Action @index (Optional)', ['@index' => $index]) :
      $this->t('Details of Enforcement Action');

    $form['action'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $action_label,
      ],
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the title'),
      '#default_value' => $this->getDefaultValuesByKey('title', $index),
    ];

    $form['regulatory_function'] = [
      '#type' => 'radios',
      '#title' => $this->t('Choose a regulatory function to which this action relates'),
      '#title_tag' => 'h2',
      '#options' => $reg_function_names,
      '#attributes' => ['class' => ['govuk-form-group']],
      '#default_value' => $this->getDefaultValuesByKey('regulatory_function', $index),
    ];

    $form['details'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide details about this action'),
      '#title_tag' => 'h2',
      '#default_value' => $this->getDefaultValuesByKey('details', $index),
    ];

    $enforcement_action_fields = $this->getEntityFieldManager()->getFieldDefinitions('par_data_enforcement_action', 'document');
    $field_definition = $enforcement_action_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Add an attachment (optional)'),
      '#title_tag' => 'h2',
      '#upload_location' => 's3private://documents/enforcement_action/',
      '#multiple' => TRUE,
      '#default_value' => $this->getDefaultValuesByKey("files", $index),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $regulatory_functions_element = $this->getElement($form, ['regulatory_function'], $index);
    $regulatory_functions = $regulatory_functions_element ? $form_state->getValue($regulatory_functions_element['#parents']) : NULL;

    if (empty($regulatory_functions)) {
      $message = 'Please choose which regulatory functions this enforcement action relates to.';
      $this->setError($form, $form_state, $regulatory_functions_element, $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
