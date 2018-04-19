<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "enforcement_action",
 *   title = @Translation("Enforcement action form.")
 * )
 */
class ParAddressForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_enforcement_action:enforcement_action' => [
      'title' => 'title',
      'details' => 'details',
      'field_regulatory_function' => 'field_regulatory_function'
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {



    parent::loadData();
  }

  /**
   * Get the country repository from the address module.
   */
  public function getCountryRepository() {
    return \Drupal::service('address.country_repository');
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $reg_function_names = $par_data_partnership ? $par_data_partnership->getPartnershipRegulatoryFunctionNames() : [];

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

    $enforcement_action_fields = $this->getEntityFieldManager()->getFieldDefinitions('par_data_enforcement_action', 'document');
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

    return $form;
  }
}
