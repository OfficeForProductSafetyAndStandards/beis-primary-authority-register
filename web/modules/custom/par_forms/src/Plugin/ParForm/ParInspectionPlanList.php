<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Inspection plan list.
 *
 * @ParForm(
 *   id = "inspection_plan_list",
 *   title = @Translation("Lists inspection plans in tabular format.")
 * )
 */
class ParInspectionPlanList extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    // If a partnership parameter is set use this to get a list of advice.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($inspection_plan_list = $par_data_partnership->getInspectionPlan()) {
        $this->getFlowDataHandler()->setParameter('inspection_plan_list', $inspection_plan_list);
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {

    $form['documentation_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['govuk-form-group']],
      '#title' => 'Advice documentation',
      '#header' => [
        'Inspection plans',
        'Status',
      ],
      '#empty' => $this->t("There is no documentation for this partnership."),
    ];

    if ($inspection_plan_list = $this->getFlowDataHandler()->getParameter('inspection_plan_list')) {
      $inspection_plan_view_builder = $this->getParDataManager()->getViewBuilder('par_data_inspection_plan');

      foreach ($inspection_plan_list as $key => $inspection_plan) {
        // The first column contains a rendered summary of the document.
        $inspection_plan_summary = $inspection_plan_view_builder->view($inspection_plan, 'summary');

        if ($inspection_plan_summary) {
          $form['documentation_list']['#rows'][$key] = [
            'data' => [
              'document' => $this->getRenderer()->render($inspection_plan_summary),
              'status' => $inspection_plan->getParStatus(),
            ],
          ];
        }

      }
    }

    return $form;
  }

}
