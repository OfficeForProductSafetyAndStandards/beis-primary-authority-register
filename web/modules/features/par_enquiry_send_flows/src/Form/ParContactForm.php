<?php

namespace Drupal\par_enquiry_send_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_enquiry_send_flows\ParFormCancelTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enquiry_send_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Contact details for enforcement officer';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $cid = $this->getFlowNegotiator()->getFormKey('par_authority_selection');
    $authority_id = $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', NULL, $cid);
    if ($authority_id && $par_data_authority = ParDataAuthority::load($authority_id)) {
      $account_id = $this->getFlowDataHandler()->getCurrentUser()->id();
      $account = User::load($account_id);

      // Get logged in user ParDataPerson(s) related to the primary authority.
      if ($par_data_person = $this->getParDataManager()->getUserPerson($account, $par_data_authority)) {
        // Set the person that's being edited.
        $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
      }
    }

    parent::loadData();
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Extract and save the enforcing officer id.
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      // Set the enforcing officer id for use later.
      $this->getFlowDataHandler()->setTempDataValue('enforcement_officer_id', $par_data_person->id());
    }
  }

}
