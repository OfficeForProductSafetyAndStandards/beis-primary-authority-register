<?php

namespace Drupal\par_authority_add_flows\Form;

use Drupal\par_authority_add_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The regulatory functions add form.
 */
class ParAuthorityRegulatoryFunctionsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Page title.
   *
   * @var ?string
   */
  protected $pageTitle = 'Provided Regulatory Functions';

}
