<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The inspection plan expiry date form.
 */
class ParPartnershipFlowsInspectionPlanDateForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $this->pageTitle = $inspection_plan ? 'Change the expiry date' : 'When does this inspeciton plan expire?';

    return parent::titleCallback();
  }

}
