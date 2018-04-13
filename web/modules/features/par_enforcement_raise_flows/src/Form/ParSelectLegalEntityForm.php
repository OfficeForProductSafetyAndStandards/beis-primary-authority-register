<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The member contact form.
 */
class ParSelectLegalEntityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Enforce legal entity';

}
