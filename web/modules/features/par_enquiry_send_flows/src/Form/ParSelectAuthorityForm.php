<?php

namespace Drupal\par_enquiry_send_flows\Form;

use Drupal\par_enquiry_send_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The member contact form.
 */
class ParSelectAuthorityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Which authority are you acting on behalf of?';

}
