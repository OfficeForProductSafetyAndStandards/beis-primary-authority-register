<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "regulatory_functions_select",
 *   title = @Translation("Select Regulatory Functions form.")
 * )
 */
class ParSelectRegulatoryFunctionsForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['regulatory_functions', 'par_data_authority', 'field_regulatory_function', NULL, NULL, 0, [
      'This value should not be null.' => 'You must choose which regulatory functions apply to this authority.',
    ],
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    $regulatory_functions = $this->getParDataManager()->getEntitiesByType('par_data_regulatory_function');
    if ($regulatory_functions) {
      $this->getFlowDataHandler()->setFormPermValue("regulatory_function_options", $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions));
    }

    // Decide which entity to use.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $existing_selection = $par_data_partnership->getRegulatoryFunction();
      $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", array_keys($this->getParDataManager()->getEntitiesAsOptions($existing_selection)));
    }
    elseif ($par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority')) {
      $existing_selection = $par_data_authority->getRegulatoryFunction();
      $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", array_keys($this->getParDataManager()->getEntitiesAsOptions($existing_selection)));
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => 'Choose regulatory functions',
      '#title_tag' => 'h2',
      '#options' => $this->getFlowDataHandler()->getFormPermValue('regulatory_function_options'),
      '#default_value' => $this->getDefaultValuesByKey('regulatory_functions', $index, []),
    ];

    return $form;
  }

}
