<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_add_flows\ParFormCancelTrait;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * The form for choosing whether to create an account for this user.
 */
class ParAccountForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Give this person a user account?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();
  }

}
