<?php

namespace Drupal\par_member_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_add_flows\ParFlowAccessTrait;

/**
 * The form for marking whether a member is covered by an inspection plan.
 */
class ParCoveredByPlanForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Inspection plan coverage";

}
