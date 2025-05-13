<?php

namespace Drupal\par_deviation_request_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_deviation_request_flows\ParFlowAccessTrait;

/**
 * Enter the date the membership began.
 */
class ParDeviationRequestForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Request deviation';

}
