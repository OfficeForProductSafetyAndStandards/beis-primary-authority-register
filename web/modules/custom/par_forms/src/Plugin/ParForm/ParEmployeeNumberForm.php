<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "employee_number",
 *   title = @Translation("Number of Employees form.")
 * )
 */
class ParAboutBusinessForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'employees_band' => 'employees_band',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    $organisation_bundle = $this->getParDataManager()->getParBundleEntity('par_data_organisation');

    // Business details.
    $form['employees_band'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of employees'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('employees_band'),
      '#options' => $organisation_bundle->getAllowedValues('employees_band'),
    ];

    return $form;
  }
}
