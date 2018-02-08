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

  protected $formItems = [
    'par_data_person:person' => [
      'first_name' => 'first_name',
      'last_name' => 'last_name',
      'work_phone' => 'work_phone',
      'mobile_phone' => 'mobile_phone',
      'email' => 'email',
      // @todo will need to look into this further on the next piece of work.
      //  'communication_email'
      //  'communication_phone'
      //  'communication_mobile'
      'communication_notes' => 'notes'
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_contact';
  }

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
      $this->pageTitle = 'Add a contact for the business';
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
      $form_state->setErrorByName('email', $this->t('<a href="#edit-email">The email field is required.</a>'));
    }

    if (empty($form_state->getValue('first_name'))) {
      $form_state->setErrorByName('first_name', $this->t('<a href="#edit-first-name">The first name field is required.</a>'));
    }

    if (empty($form_state->getValue('last_name'))) {
      $form_state->setErrorByName('last_name', $this->t('<a href="#edit-last-name">The last name field is required.</a>'));
    }

    if (empty($form_state->getValue('work_phone'))) {
      $form_state->setErrorByName('work_phone', $this->t('<a href="#edit-work-phone">The work phone field is required.</a>'));
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
