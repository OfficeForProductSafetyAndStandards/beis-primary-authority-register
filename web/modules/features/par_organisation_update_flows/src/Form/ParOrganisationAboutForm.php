<?php

namespace Drupal\par_organisation_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_organisation_update_flows\ParFlowAccessTrait;

/**
 * The about the organisation form.
 */
class ParOrganisationAboutForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'About the organisation';

}
