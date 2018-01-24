<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "sic_code",
 *   title = @Translation("SIC Code form.")
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
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    $sic_codes = $this->getParDataManager()->getEntitiesByType();

    // Display the correct introductory text based on the action that is being performed.
    $intro_text = $this->getFlowDataHandler()->getDefaultValues("sic_code", NULL) ?
      'Change the SIC Code of your organisation' :
      'Add a new SIC Code to your organisation';

    $form['sic_code'] = [
      '#type' => 'select',
      '#title' => $this->t($intro_text),
      '#options' => $this->getParDataManager()->getEntitiesAsOptions($sic_codes),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("sic_code", NULL),
    ];

    return $form;
  }
}
