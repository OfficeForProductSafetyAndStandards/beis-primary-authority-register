<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParPartnershipFlowsInspectionPlanListController extends ParBaseController {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Inspection Plans';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    $form['inspection_plan_help_text'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('How to upload an Inspection Plan'),
      '#description' => $this->t('To upload an Inspection Plan, email it to <a href="mailto:pa@beis.gov.uk">pa@beis.gov.uk</a> with details of the organisation it applies to and we’ll get back to you shortly.'),
    ];

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
            'status' => $inspection_plan->getParStatus(),
          ],
        ];
      }

    }

    // Only allow inspection plan uploading on active partnerships as only active partnerships.
    // Hide upload button when user is on the search path.
    if ($par_data_partnership->isActive() && $this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
      $build['actions'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group']],
      ];

      try {
        $build['actions']['upload'] = [
          '#type' => 'markup',
          '#markup' => '<br>' . t('@link', [
              '@link' => $this->getFlowNegotiator()
                  ->getFlow()
                  ->getNextLink('upload', $this->getRouteParams())
                  ->setText('Upload inspection plan')
                  ->toString(),
            ]),
        ];
      }
      catch (ParFlowException $e) {

      }
    }

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}
