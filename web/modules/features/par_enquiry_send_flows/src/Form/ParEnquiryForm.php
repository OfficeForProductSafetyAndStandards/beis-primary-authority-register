<?php

namespace Drupal\par_enquiry_send_flows\Form;

use Drupal\par_enquiry_send_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Enter the enquiry.
 */
class ParEnquiryForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Enquiry';

}
