<?php

namespace Drupal\par_deviation_request_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_deviation_request_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The member contact form.
 */
class ParSelectInspectionPlanForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Choose an inspection plan';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the inspection plan data set by the select_inspection form plugin.
    $inspection_plans = $this->getFlowDataHandler()->getFormPermValue('inspection_plan_options');
    if ($inspection_plans && count($inspection_plans) <= 0) {
      $this->getFlowNegotiator()->getFlow()->disableAction('next');
    }

    return parent::buildForm($form, $form_state);
  }

}
