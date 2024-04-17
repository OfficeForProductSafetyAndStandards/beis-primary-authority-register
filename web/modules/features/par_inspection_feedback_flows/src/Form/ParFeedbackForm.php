<?php

namespace Drupal\par_inspection_feedback_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_inspection_feedback_flows\ParFlowAccessTrait;

/**
 * Enter the date the membership began.
 */
class ParFeedbackForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Feedback';

}
