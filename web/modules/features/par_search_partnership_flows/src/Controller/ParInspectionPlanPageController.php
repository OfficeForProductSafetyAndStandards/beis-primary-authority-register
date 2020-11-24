<?php

namespace Drupal\par_search_partnership_flows\Controller;


use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\Controller\ParBaseInterface;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The Inspection plan entity details page.
 */
class ParInspectionPlanPageController extends ParBaseController {

  use ParPartnershipFlowAccessTrait;
  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $par_data_partnership_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    if ($par_data_partnership_inspection_plan) {
      $this->pageTitle = $par_data_partnership_inspection_plan->getTitle();
    }
    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {

    if ($par_data_inspection_plan->isRevoked()) {
      $build['inspection_plan_details'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $build['inspection_plan_details']['revoked'] = [
        '#type' => 'markup',
        '#markup' => 'Revoked inspection plan',
        '#prefix' => '<h2>',
        '#suffix' => '</h2>',
      ];
    }

    $build['summary'] = $this->renderSection('About this inspection plan document', $par_data_inspection_plan, ['summary' => 'summary']);

    $build['valid_date'] = $this->renderSection('The date range this inspection plan is valid for', $par_data_inspection_plan, ['valid_date' => 'full']);

    $build['inspection_plan_link'] = $this->renderSection('Inspection plan documents', $par_data_inspection_plan, ['field_document' => 'title']);

    return parent::build($build);
  }
}
