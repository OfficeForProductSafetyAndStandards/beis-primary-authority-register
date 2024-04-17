<?php

namespace Drupal\par_invite_user_flows\Form;

use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for linking a contact to a user.
 */
class ParLinkContactForm extends ParBaseForm {

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Link this person to a user account?';

  /**
   * Load the data for this.
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
