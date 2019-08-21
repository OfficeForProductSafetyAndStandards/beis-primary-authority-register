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
  protected $entityMapping = [];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
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

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $form['regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => 'Choose regulatory functions',
      '#options' => $this->getFlowDataHandler()->getFormPermValue('regulatory_function_options'),
      '#default_value' => $this->getDefaultValuesByKey('regulatory_functions', $cardinality, []),
    ];

    return $form;
  }
}
