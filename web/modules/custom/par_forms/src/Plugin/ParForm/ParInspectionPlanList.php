<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
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
  public function loadData($cardinality = 1) {
    // If a partnership parameter is set use this to get a list of advice.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($inspection_plan_list = $par_data_partnership->getInspectionPlan()) {
        $this->getFlowDataHandler()->setParameter('inspection_plan_list', $inspection_plan_list);
      }
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['documentation_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
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
