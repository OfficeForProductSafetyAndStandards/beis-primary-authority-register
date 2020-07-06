<?php

namespace Drupal\par_authority_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_authority_add_flows\ParFlowAccessTrait;

/**
 * The authority title form.
 */
class ParAuthorityTitleForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Authority Name';

}
