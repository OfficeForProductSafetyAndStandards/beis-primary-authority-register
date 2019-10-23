<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Inspection Plan expire date form plugin.
 *
 * @ParForm(
 *   id = "inspection_plan_date",
 *   title = @Translation("Member begin date.")
 * )
 */
class ParInspectionPlanDateForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
  /*  ['inspection_plan_expire', 'par_data_inspection_plan', 'valid_date', NULL, NULL, 0, [
      'This value should not be null.' => 'You must enter the date the inspection plan expires e.g. 2017-9-22.'
    ]],*/
  ];

  /**
   * @defaults
   */
  public function getFormDefaults() {
    return [
      'inspection_plan_expire' => ['year' => date('Y'), 'month' => date('m'), 'day' => date('d')],
    ];
  }

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan')) {
      // Inspection plan start date.
      $start_date = $par_data_inspection_plan->get('valid_date')->value;
      if (isset($start_date)) {
        $this->getFlowDataHandler()->setFormPermValue('inspection_plan_begin', $start_date);
      }

      // Inspection plan end date.
      $end_date = $par_data_inspection_plan->get('valid_date')->end_value;
      if (isset($end_date)) {
        $this->getFlowDataHandler()->setFormPermValue('expire', $end_date);
      }
    }

    $start_date_required = isset($this->getConfiguration()['start_date']) ? (bool) $this->getConfiguration()['start_date'] : true;
    $this->getFlowDataHandler()->setFormPermValue("start_date_required", $start_date_required);

    $end_date_required = isset($this->getConfiguration()['end_date']) ? (bool) $this->getConfiguration()['end_date'] : true;
    $this->getFlowDataHandler()->setFormPermValue("end_date_required", $end_date_required);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Inspection plan begin date.
    if ($this->getFlowDataHandler()->getFormPermValue("start_date_required")) {
      $form['inspection_plan_begin'] = [
        '#type' => 'gds_date',
        '#title' => $this->t('Enter the date the inspection plan is valid from'),
        '#description' => $this->t('For example: 12/01/2019'),
        '#default_value' => $this->getDefaultValuesByKey('inspection_plan_begin', $cardinality, $this->getFormDefaultByKey('inspection_plan_begin')),
      ];
    }

    // Inspection plan begin date.
    if ($this->getFlowDataHandler()->getFormPermValue("end_date_required")) {
      $form['expire'] = [
        '#type' => 'gds_date',
        '#title' => $this->t('Enter the date the inspection plan expires'),
        '#description' => $this->t('For example: 12/01/2021'),
        '#default_value' => $this->getDefaultValuesByKey('expire', $cardinality, $this->getFormDefaultByKey('expire')),
      ];
    }

    return $form;
  }
}
