<?php

namespace Drupal\par_partnership_contact_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_contact_update_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_update_flows\ParFormCancelTrait;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add contact details';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $cid_person_select = $this->getFlowNegotiator()->getFormKey('par_choose_person');
    $person = $this->getFlowDataHandler()->getDefaultValues('user_person', '', $cid_person_select);
    if ($par_data_person = ParDataPerson::load($person)) {
      $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
    }

    parent::loadData();
  }

}
