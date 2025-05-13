<?php

namespace Drupal\par_member_add_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_add_flows\ParFlowAccessTrait;

/**
 * Add trading names.
 */
class ParTradingForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Add trading name";

}
