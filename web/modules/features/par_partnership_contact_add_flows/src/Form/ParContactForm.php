<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_add_flows\ParFormCancelTrait;

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

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    switch ($this->getFlowDataHandler()->getParameter('type')) {
      case 'authority':
        $contacts = $par_data_partnership->getAuthorityPeople();
        break;

      case 'organisation':
        $contacts = $par_data_partnership->getOrganisationPeople();
        break;

      default:
        $contacts = [];

    }

    foreach ($contacts as $contact) {
      if (!empty($form_state->getValue('email'))
          && $contact->getEmail() === $form_state->getValue('email')) {
        $id = $this->getElementId(['email'], $form);
        $form_state->setErrorByName($this->getElementName('email'), $this->wrapErrorMessage('This person has already been added, you cannot add the same person again. Please change the e-mail address and try again.', $id));

      }
    }

    parent::validateForm($form, $form_state);
  }

}
