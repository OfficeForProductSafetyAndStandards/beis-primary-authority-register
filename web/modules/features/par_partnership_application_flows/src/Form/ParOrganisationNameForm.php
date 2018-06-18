<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The organisation name form.
 */
class ParOrganisationNameForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Who are you in partnership with?';

}
