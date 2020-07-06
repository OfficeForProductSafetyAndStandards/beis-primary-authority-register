<?php

namespace Drupal\par_authority_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_update_flows\ParFlowAccessTrait;

/**
 * The regulatory functions update form.
 */
class ParAuthorityRegulatoryFunctionsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Provided Regulatory Functions';

}
