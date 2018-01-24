<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParContactForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm the primary contact details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_contact';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $par_data_person = $partnership ? $partnership->getOrganisationPeople(TRUE) : NULL;

    // Override the route parameter so that data loaded will be from this entity.
    $this->getflowDataHandler()->setParameter('par_data_person', $par_data_person);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save contact.
    $par_data_person = $this->getflowDataHandler()->getParameter('par_data_partnership');

    // Save person details.
    if ($par_data_person) {
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation'));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name'));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name'));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone'));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone'));
      $par_data_person->set('email', $this->getFlowDataHandler()->getTempDataValue('email'));
      $par_data_person->set('communication_notes', $this->getFlowDataHandler()->getTempDataValue('notes'));

      // Save the contact preferences
      $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact')['communication_email'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact')['communication_email']);
      $par_data_person->set('communication_email', $email_preference_value);
      // Save the work phone preference.
      $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact')['communication_phone'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact')['communication_phone']);
      $par_data_person->set('communication_phone', $work_phone_preference_value);
      // Save the mobile phone preference.
      $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact')['communication_mobile'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact')['communication_mobile']);
      $par_data_person->set('communication_mobile', $mobile_phone_preference_value);

      if ($par_data_person->save()) {
        // Only delete the form data for the par_partnership_contact form.
        $this->getFlowDataHandler()->deleteFormTempData('par_partnership_contact');
      }
      else {
        $message = $this->t('This %person could not be saved for %form_id');
        $replacements = [
          '%person' => $this->getFlowDataHandler()->getTempDataValue('name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }

  }

}
