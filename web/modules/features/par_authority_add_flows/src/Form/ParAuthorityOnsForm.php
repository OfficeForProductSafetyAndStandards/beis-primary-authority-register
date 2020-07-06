<?php

namespace Drupal\par_authority_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_add_flows\ParFlowAccessTrait;

/**
 * The ons code add form.
 */
class ParAuthorityOnsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'ONS Code';

}
