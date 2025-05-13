<?php

namespace Drupal\par_inspection_feedback_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_inspection_feedback_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParSelectAuthorityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Which authority are you acting on behalf of?';

}
