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

}
