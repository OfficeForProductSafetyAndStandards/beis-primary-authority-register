<?php

namespace Drupal\par_organisation_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_organisation_update_flows\ParFlowAccessTrait;

/**
 * The SIC Codes update form.
 */
class ParOrganisationSicCodesForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'SIC Codes';

}
