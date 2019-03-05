<?php

namespace Drupal\par_person_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_person_update_flows\ParFlowAccessTrait;

/**
 * The form for linking a contact to a user.
 */
class ParLinkContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Link this person to a user account?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $account = $par_data_person->getUserAccount();
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    parent::loadData();
  }

}
