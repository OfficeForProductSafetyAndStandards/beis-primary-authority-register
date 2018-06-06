<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add a contact for the organisation';

}
