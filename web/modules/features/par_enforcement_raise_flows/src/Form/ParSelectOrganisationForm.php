<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The member contact form.
 */
class ParSelectOrganisationForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Enforce member';

}
