<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * Partnership Application Form - Type radios page.
 */
class ParApplicationTypeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'What kind of partnership are you applying for?';

}
