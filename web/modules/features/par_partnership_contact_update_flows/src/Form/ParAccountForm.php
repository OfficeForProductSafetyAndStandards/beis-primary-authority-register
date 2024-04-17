<?php

namespace Drupal\par_partnership_contact_update_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_contact_update_flows\ParFlowAccessTrait;

/**
 * The form for choosing whether to create an account for this user.
 */
class ParAccountForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Give this person a user account?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();
  }

}
