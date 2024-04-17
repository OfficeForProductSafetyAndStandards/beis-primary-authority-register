<?php

namespace Drupal\par_deviation_request_flows\Form;

use Drupal\par_deviation_request_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Enter the date the membership began.
 */
class ParDeviationRequestForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Request deviation';

}
