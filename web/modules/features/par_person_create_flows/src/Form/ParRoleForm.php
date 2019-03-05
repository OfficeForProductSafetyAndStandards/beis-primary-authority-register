<?php

namespace Drupal\par_person_create_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_person_create_flows\ParFlowAccessTrait;
use Drupal\user\Entity\Role;

/**
 * The form for choosing which role to grant a user.
 */
class ParRoleForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'What type of user would you like to create?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {

    parent::loadData();
  }

}
