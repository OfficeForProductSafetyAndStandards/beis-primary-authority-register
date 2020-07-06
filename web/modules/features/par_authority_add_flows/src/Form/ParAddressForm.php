<?php

namespace Drupal\par_authority_add_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_add_flows\ParFlowAccessTrait;

/**
 * The form for the premises details.
 */
class ParAddressForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add address';

}
