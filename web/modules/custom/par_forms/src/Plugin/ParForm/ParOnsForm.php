<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "authority_ons",
 *   title = @Translation("ONS code form.")
 * )
 */
class ParOnsForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['sic_code', 'par_data_authority', 'ons_code', NULL, NULL, 0, [
      'You must fill in the missing information.' => "You must enter an ONS code."
    ]],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');

    if ($par_data_authority && $ons_code = $par_data_authority->get('ons_code')) {
      $this->getFlowDataHandler()->setFormPermValue("ons_code", $ons_code->getString());
    }


    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $form['ons_code'] = [
      '#type' => 'textfield',
      '#title' => 'Enter the ONS code',
      '#description' => 'The Office for National Statistics maintains a series of codes to represent a wide range of geographical areas of the UK including local authorities.',
      '#default_value' => $this->getDefaultValuesByKey('ons_code', $cardinality, NULL),
    ];

    return $form;
  }
}
