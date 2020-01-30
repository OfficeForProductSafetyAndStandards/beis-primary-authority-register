<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "partnership_regulatory_functions",
 *   title = @Translation("Partnership Regulatory Functions form.")
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
    // Decide which entity to use.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $existing_selection = $par_data_partnership->getRegulatoryFunction();
      $this->getFlowDataHandler()->setFormPermValue("regulatory_functions", array_keys($this->getParDataManager()->getEntitiesAsOptions($existing_selection)));

      // Get the available options.
      if ($authority = $par_data_partnership->getAuthority()) {
        $available_regulatory_functions = $authority->getRegulatoryFunctions();
        $this->getFlowDataHandler()->setFormPermValue("regulatory_function_options", $this->getParDataManager()->getEntitiesAsOptions($available_regulatory_functions));
      }
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
