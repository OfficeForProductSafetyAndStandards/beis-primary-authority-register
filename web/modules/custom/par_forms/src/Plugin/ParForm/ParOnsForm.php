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
  protected array $entityMapping = [
    ['ons_code', 'par_data_authority', 'ons_code', NULL, NULL, 0, [
      'You must fill in the missing information.' => "You must enter an ONS code.",
    ],
    ],
  ];

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');

    if ($par_data_authority && $ons_code = $par_data_authority->get('ons_code')) {
      $this->getFlowDataHandler()->setFormPermValue("ons_code", $ons_code->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $form['ons_code'] = [
      '#type' => 'textfield',
      '#title' => 'Enter the ONS code',
      '#description' => 'The Office for National Statistics maintains a series of codes to represent a wide range of geographical areas of the UK including local authorities.',
      '#default_value' => $this->getDefaultValuesByKey('ons_code', $index, NULL),
    ];

    return $form;
  }

}
