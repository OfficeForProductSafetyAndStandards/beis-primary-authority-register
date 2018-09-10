<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * A form for submission of general enquiries.
 *
 * @ParForm(
 *   id = "select_inspection_plan",
 *   title = @Translation("Select inspection plan form.")
 * )
 */
class ParSelectInspectionPlanForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($inspection_plans = $par_data_partnership->getInspectionPlan()) {
        $options = $this->getParDataManager()->getEntitiesAsOptions((array) $inspection_plans);
        $this->getFlowDataHandler()->setFormPermValue('inspection_plan_options', $options);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $inspection_plans = $this->getFlowDataHandler()->getFormPermValue('inspection_plan_options');

    if (count($inspection_plans) <= 0) {
      $form['no_inspection_plans'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('There are no inspection plans'),
        'text' => [
          '#type' => 'markup',
          '#markup' => $this->t("This partnership is not covered by any inspection plans, please contact the primary authority to request an inspection plan."),
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ],
      ];

      return $form;
    }
    elseif (count($inspection_plans) === 1) {
      $this->getFlowDataHandler()->setTempDataValue('inspection_plan_id', key($inspection_plans));
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    // Checkboxes for inspection plans.
    $form['inspection_plan_id'] = [
      '#type' => 'checkboxes',
      '#attributes' => ['class' => ['form-group']],
      '#title' => t('Choose which inspection plan you\'d like to deviate from'),
      '#options' => $inspection_plans,
      // Automatically check all legal entities if no form data is found.
      '#default_value' => $this->getDefaultValuesByKey('inspection_plan', $cardinality, NULL),
    ];

    return $form;
  }
}
