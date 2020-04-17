<?php

namespace Drupal\par_inspection_feedback_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_inspection_feedback_flows\ParFormCancelTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_inspection_feedback_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The member contact form.
 */
class ParSelectInspectionPlanForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Choose an inspection plan';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the inspection plan data set by the select_inspection form plugin.
    $inspection_plans = $this->getFlowDataHandler()->getFormPermValue('inspection_plan_options');
    if (count($inspection_plans) <= 0) {
      $this->getFlowNegotiator()->getFlow()->disableAction('next');
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $selected_inspection_plans = (array) $form_state->getValue('inspection_plan_id');

    if (!empty(array_filter($selected_inspection_plans))) {
      $id = $this->getElementId(['inspection_plan_id'], $form);
      $form_state->setErrorByName($this->getElementName('inspection_plan_id'), $this->wrapErrorMessage('You must select at least one inspection plan to feedback on.', $id));
    };
  }

}
