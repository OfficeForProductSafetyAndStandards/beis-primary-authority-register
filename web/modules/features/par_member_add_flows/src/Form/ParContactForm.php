<?php

namespace Drupal\par_member_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_add_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add member contact details';

}
