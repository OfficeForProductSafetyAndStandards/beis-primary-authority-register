<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParSelectOrganisationForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Enforce member';

}
