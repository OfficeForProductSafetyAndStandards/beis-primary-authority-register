<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsContactForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['first_name', 'par_data_person', 'first_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the first name for this contact.'
    ]],
    ['last_name', 'par_data_person', 'last_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the last name for this contact.'
    ]],
    ['work_phone', 'par_data_person', 'work_phone', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the work phone number for this contact.'
    ]],
    ['mobile_phone', 'par_data_person', 'mobile_phone', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the mobile phone number for this contact.'
    ]],
    ['email', 'par_data_person', 'email', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the email address for this contact.'
    ]],
    ['notes', 'par_data_person', 'communication_notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter any communication notes that are relevant to this contact.'
    ]],
  ];

  /**
   * Get partnership.
   */
  public function getPartnershipParam() {
    return $this->getFlowDataHandler()->getParameter('par_data_partnership');
  }

  /**
   * Get partnership.
   */
  public function getPersonParam() {
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_direct_application' || $this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated_application') {
      $partnership = $this->getPartnershipParam();
      $people = $partnership ? $partnership->getOrganisationPeople() : NULL;
      return !empty($people) ? current($people) : NULL;
    }
    else {
      return $this->getFlowDataHandler()->getParameter('par_data_person');
    }
  }

  public function titleCallback() {
    // Check if editing an existing entity.
    $par_data_person = $this->getPersonParam();

    // Display appropriate title.
    $this->pageTitle = $par_data_person ? 'Edit a contact' : 'Add a contact';

    // Override page title for Partnership Application journey.
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_application') {
      $this->pageTitle = 'Add a contact for the organisation';
    }

    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_direct_application' || $this->getFlowNegotiator()->getFlowName() === 'partnership_coordinated_application') {
      $this->pageTitle = 'Confirm the primary contact details';
    }

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataPerson $par_data_person
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    if ($par_data_person) {
      // Load person data.
      $this->getFlowDataHandler()->setFormPermValue("salutation", $par_data_person->get('salutation')->getString());
      $this->getFlowDataHandler()->setFormPermValue("first_name", $par_data_person->get('first_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("last_name", $par_data_person->get('last_name')->getString());
      $this->getFlowDataHandler()->setFormPermValue("work_phone", $par_data_person->get('work_phone')->getString());
      $this->getFlowDataHandler()->setFormPermValue("mobile_phone", $par_data_person->get('mobile_phone')->getString());
      $this->getFlowDataHandler()->setFormPermValue("email", $par_data_person->get('email')->getString());
      $this->getFlowDataHandler()->setFormPermValue("notes", $par_data_person->get('communication_notes')->getString());

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => $par_data_person->getBoolean('communication_email'),
        'communication_phone' => $par_data_person->getBoolean('communication_phone'),
        'communication_mobile' => $par_data_person->getBoolean('communication_mobile'),
      ];

      // Checkboxes works nicely with keys, filtering booleans for "1" value.
      $this->getFlowDataHandler()->setFormPermValue('preferred_contact', array_keys($contact_options, 1));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $par_data_person = $this->getPersonParam();
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);

    $type = \Drupal::service('plugin.manager.par_form_builder');
    $plugin = $type->createInstance('contact_details_full');

    $form = $plugin->getElements($form);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate required fields.
    // @todo create wrapper for setErrorByName as this is ugly creating a link.
    if (empty($form_state->getValue('email'))) {
      $id = $this->getElementId(['email'], $form);
      $form_state->setErrorByName($this->getElementName('email'), $this->wrapErrorMessage('You must enter an email address.', $id));
    }

    if (empty($form_state->getValue('first_name'))) {
      $id = $this->getElementId(['first_name'], $form);
      $form_state->setErrorByName($this->getElementName('first_name'), $this->wrapErrorMessage('You must enter a first name.', $id));
    }

    if (empty($form_state->getValue('last_name'))) {
      $id = $this->getElementId(['last_name'], $form);
      $form_state->setErrorByName($this->getElementName('last_name'), $this->wrapErrorMessage('You must enter a last name.', $id));
    }

    if (empty($form_state->getValue('work_phone'))) {
      $id = $this->getElementId(['work_phone'], $form);
      $form_state->setErrorByName($this->getElementName('work_phone'), $this->wrapErrorMessage('You must enter the work telephone number.', $id));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save contact.
    $par_data_person = $this->getPersonParam();

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
        // Only delete the form data for the current form.
        $this->getFlowDataHandler()->deleteFormTempData();
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
