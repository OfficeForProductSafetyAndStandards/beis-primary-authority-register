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
 * The inspection plan entity details page.
 */
class ParPartnershipFlowsInspectionPlanPageController extends ParBaseController {

  use ParPartnershipFlowAccessTrait;
  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    if ($par_data_inspection_plan->isRevoked()) {
      $build['inspection_plan_details'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group', 'notice']],
      ];
      $build['inspection_plan_details']['revoked'] = [
        '#type' => 'markup',
        '#markup' => '<i class="icon icon-important"><span class="govuk-visually-hidden">Revoked</span></i>',
      ];
      $build['inspection_plan_details']['warning'] = [
        '#type' => 'markup',
        '#markup' => '<strong class="bold-small">This inspection plan has been revoked and is for reference only, please do not use it for an inspection.</strong>',
      ];
    }

    $build['summary'] = $this->renderSection('About this inspection plan document', $par_data_inspection_plan, ['summary' => 'summary']);

    $build['valid_date'] = $this->renderSection('The date range this inspection plan is valid for', $par_data_inspection_plan, ['valid_date' => 'full']);

    $build['inspection_plan_link'] = $this->renderSection('Inspection plan documents', $par_data_inspection_plan, ['document' => 'title']);

    // Make sure to add the inspection plan cacheability data to this form.
    $this->addCacheableDependency($par_data_inspection_plan);

    return parent::build($build);
  }
}
