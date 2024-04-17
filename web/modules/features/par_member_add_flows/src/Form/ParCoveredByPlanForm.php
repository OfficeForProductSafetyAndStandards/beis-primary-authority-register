<?php

namespace Drupal\par_member_add_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_add_flows\ParFlowAccessTrait;

/**
 * The form for marking whether a member is covered by an inspection plan.
 */
class ParCoveredByPlanForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = "Inspection plan coverage";

}
