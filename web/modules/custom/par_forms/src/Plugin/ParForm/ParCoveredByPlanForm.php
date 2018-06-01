<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "covered_by_plan",
 *   title = @Translation("Covered by Inspection Plan form.")
 * )
 */
class ParCoveredByPlanForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_coordinated_business:coordinated_business' => [
      'covered_by_inspection' => 'covered_by_inspection',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($coordinated_member = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business')) {
      $this->getFlowDataHandler()->setFormPermValue('covered_by_inspection', $coordinated_member->getBoolean('covered_by_inspection'));
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $coordinated_member_bundle = $this->getParDataManager()->getParBundleEntity('par_data_coordinated_business');

    // Inspection plan coverage.
    $form['covered_by_inspection'] = [
      '#type' => 'radios',
      '#title' => $this->t('Is this member covered by an inspection plan?'),
      '#default_value' => $this->getDefaultValuesByKey('covered_by_inspection', $cardinality),
      '#options' => [
        1 => $coordinated_member_bundle->getBooleanFieldLabel('covered_by_inspection', TRUE),
        0 => $coordinated_member_bundle->getBooleanFieldLabel('covered_by_inspection', FALSE),
      ],
    ];

    return $form;
  }
}
