<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "sic_code",
 *   title = @Translation("SIC code form.")
 * )
 */
class ParSicCodeForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'field_sic_code' => 'sic_code',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    $sic_code_delta = $this->getFlowDataHandler()->getParameter('sic_code_delta');
    if ($par_data_organisation) {
      // Store the current value of the trading name if it's being edited.
      $sic_code = $par_data_organisation ? $par_data_organisation->get('field_sic_code')->get($sic_code_delta) : NULL;

      if ($sic_code) {
        $this->getFlowDataHandler()->setFormPermValue("sic_code", $sic_code->getString());
      }
    }


    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $sic_codes = $this->getParDataManager()->getEntitiesByType('par_data_sic_code');

    // Display the correct introductory text based on the action that is being performed.
    $intro_text = $this->getFlowDataHandler()->getDefaultValues("sic_code", NULL) ?
      'Change the SIC Code of your organisation' :
      'Add a new SIC Code to your organisation';

    $form['sic_code'] = [
      '#type' => 'select',
      '#title' => $this->t($intro_text),
      '#options' => $this->getParDataManager()->getEntitiesAsOptions($sic_codes),
      '#default_value' => $this->getDefaultValuesByKey('sic_code', $cardinality, NULL),
    ];

    return $form;
  }
}
