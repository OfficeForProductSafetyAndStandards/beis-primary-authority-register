<?php

namespace Drupal\par_enquiry_send_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enquiry_send_flows\ParFlowAccessTrait;

/**
 * Enter the enquiry.
 */
class ParEnquiryForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Enquiry';

}
