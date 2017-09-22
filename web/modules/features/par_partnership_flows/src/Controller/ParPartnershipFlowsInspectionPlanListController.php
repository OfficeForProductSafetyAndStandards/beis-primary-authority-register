<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParPartnershipFlowsInspectionPlanListController extends ParBaseController {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    $build['documentation_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
      '#title' => 'Advice documentation',
      '#header' => [
        'Inspection plans',
        'Status',
      ],
      '#empty' => $this->t("There is no documentation for this partnership."),
    ];

    // Get each Advice document and add as a table row.
    foreach ($par_data_partnership->getInspectionPlan() as $inspection_plan) {
      $inspection_plan_view_builder = $this->getParDataManager()->getViewBuilder('par_data_inspection_plan');

      // The first column contains a rendered summary of the document.
      $inspection_plan_summary = $inspection_plan_view_builder->view($inspection_plan, 'summary');

      if ($inspection_plan_summary) {
        $build['documentation_list']['#rows'][] = [
          'data' => [
            'document' => $this->getRenderer()->render($inspection_plan_summary),
            'status' => $inspection_plan->retrieveStringValue('inspection_status'),
          ],
        ];
      }

    }

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}
