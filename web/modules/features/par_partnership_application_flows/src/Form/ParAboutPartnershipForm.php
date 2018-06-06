<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The partnership form for the about partnership details.
 */
class ParAboutPartnershipForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Information about the new partnership';

}
