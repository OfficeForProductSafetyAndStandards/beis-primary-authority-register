<?php

namespace Drupal\par_enquiry_send_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enquiry_send_flows\ParFlowAccessTrait;

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
