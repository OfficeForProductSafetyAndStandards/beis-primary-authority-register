<?php

namespace Drupal\par_partnership_flows\Controller;


use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Controller\ParBaseInterface;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The Advice entity details page.
 */
class ParPartnershipFlowsInspectionPlanPageController extends ParBaseController {

  use ParPartnershipFlowAccessTrait;
  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $par_data_partnership_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    if ($par_data_partnership_inspection_plan) {
      $this->pageTitle = $par_data_partnership_inspection_plan->getAdviceTitle();
    }
    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {

    if ($par_data_inspection_plan->isArchived()) {
      $build['inspection_plan_details'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $build['inspection_plan_details']['archived'] = [
        '#type' => 'markup',
        '#markup' => 'Archived advice',
        '#prefix' => '<h2>',
        '#suffix' => '</h2>',
      ];
    }

    $build['notes'] = $this->renderSection('About this advice document', $par_data_inspection_plan, ['notes' => 'summary']);

    $build['inspection_plan_type'] = $this->renderSection('The type of advice', $par_data_inspection_plan, ['inspection_plan_type' => 'summary']);

    $build['regulatory_functions'] = $this->renderSection('Regulatory functions', $par_data_inspection_plan, ['field_regulatory_function' => 'full']);

    $build['issue_date'] = $this->renderSection('Issue date', $par_data_inspection_plan, ['issue_date' => 'full']);

    $build['inspection_plan_link'] = $this->renderSection('Advice documents', $par_data_inspection_plan, ['document' => 'title']);

    return parent::build($build);
  }
}
