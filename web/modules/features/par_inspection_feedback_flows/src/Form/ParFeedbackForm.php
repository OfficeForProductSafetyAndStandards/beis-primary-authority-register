<?php

namespace Drupal\par_inspection_feedback_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_inspection_feedback_flows\ParFormCancelTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_inspection_feedback_flows\ParFlowAccessTrait;

/**
 * Enter the date the membership began.
 */
class ParFeedbackForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Feedback';

}
