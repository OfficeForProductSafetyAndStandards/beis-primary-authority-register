<?php

namespace Drupal\par_profile_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;

/**
 * The form for linking a contact to a user.
 */
class ParLinkContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Would type of user would you like to add?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {

    parent::loadData();
  }

}
