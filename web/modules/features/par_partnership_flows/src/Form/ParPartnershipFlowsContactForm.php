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

  public function titleCallback() {
    // Check if editing an existing entity.
    $par_data_person = $this->getRouteParam('par_data_person');

    // Display appropriate title.
    $this->pageTitle = $par_data_person ? 'Edit a contact' : 'Add a contact';

    // Override page title for Partnership Application journey.
    if ($this->getFlowName() === 'partnership_application') {
      $this->pageTitle = 'Add a contact for the business';
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

      $this->setState("edit:{$par_data_person->id()}");

      // Load person data.
      $this->loadDataValue("salutation", $par_data_person->get('salutation')->getString());
      $this->loadDataValue("first_name", $par_data_person->get('first_name')->getString());
      $this->loadDataValue("last_name", $par_data_person->get('last_name')->getString());
      $this->loadDataValue("work_phone", $par_data_person->get('work_phone')->getString());
      $this->loadDataValue("mobile_phone", $par_data_person->get('mobile_phone')->getString());
      $this->loadDataValue("email", $par_data_person->get('email')->getString());
      $this->loadDataValue("notes", $par_data_person->get('communication_notes')->getString());

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => $par_data_person->getBoolean('communication_email'),
        'communication_phone' => $par_data_person->getBoolean('communication_phone'),
        'communication_mobile' => $par_data_person->getBoolean('communication_mobile'),
      ];

      // Checkboxes works nicely with keys, filtering booleans for "1" value.
      $this->loadDataValue('preferred_contact', array_keys($contact_options, 1));

    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');

    if ($this->getFlowName() == 'partnership_application') {
      $form['info'] = [
        '#type' => 'markup',
        '#markup' => $this->t("Providing contact information"),
        '#prefix' => '<h2>',
        '#suffix' => '</h2>',
      ];

      $form['help_text'] = [
        '#type' => 'markup',
        '#markup' => $this->t('State who is the main contact for the business. It might be the business owner, or an employee in charge of regulatory compliance.'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }

    $form['salutation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the title (optional)'),
      '#description' => $this->t('For example, Ms Mr Mrs Dr'),
      '#default_value' => $this->getDefaultValues("salutation"),
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the first name'),
      '#default_value' => $this->getDefaultValues("first_name"),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the last name'),
      '#default_value' => $this->getDefaultValues("last_name"),
    ];

    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the work phone number'),
      '#default_value' => $this->getDefaultValues("work_phone"),
    ];

    $form['mobile_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the mobile phone number (optional)'),
      '#default_value' => $this->getDefaultValues("mobile_phone"),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Enter the email address'),
      '#default_value' => $this->getDefaultValues("email"),
      // Prevent modifying email if editing an existing user.
      '#disabled' => !empty($par_data_person),
    ];

    // Get preferred contact methods labels.
    $contact_options = [
      'communication_email' => $person_bundle->getBooleanFieldLabel('communication_email', 'on'),
      'communication_phone' => $person_bundle->getBooleanFieldLabel('communication_phone', 'on'),
      'communication_mobile' => $person_bundle->getBooleanFieldLabel('communication_mobile', 'on'),
    ];

    $form['preferred_contact'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the preferred methods of contact (optional)'),
      '#options' => $contact_options,
      '#default_value' => $this->getDefaultValues("preferred_contact", []),
      '#return_value' => 'on',
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide contact notes (optional)'),
      '#default_value' => $this->getDefaultValues('notes'),
      '#description' => 'Add any additional notes about how best to contact this person.',
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_person);
    $this->addCacheableDependency($person_bundle);

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
    $par_data_person = $this->getRouteParam('par_data_person');

    // Save person details.
    if ($par_data_person) {
      $par_data_person->set('salutation', $this->getTempDataValue('salutation'));
      $par_data_person->set('first_name', $this->getTempDataValue('first_name'));
      $par_data_person->set('last_name', $this->getTempDataValue('last_name'));
      $par_data_person->set('work_phone', $this->getTempDataValue('work_phone'));
      $par_data_person->set('mobile_phone', $this->getTempDataValue('mobile_phone'));
      $par_data_person->set('email', $this->getTempDataValue('email'));
      $par_data_person->set('communication_notes', $this->getTempDataValue('notes'));

      // Save the contact preferences
      $email_preference_value = isset($this->getTempDataValue('preferred_contact')['communication_email'])
        && !empty($this->getTempDataValue('preferred_contact')['communication_email']);
      $par_data_person->set('communication_email', $email_preference_value);
      // Save the work phone preference.
      $work_phone_preference_value = isset($this->getTempDataValue('preferred_contact')['communication_phone'])
        && !empty($this->getTempDataValue('preferred_contact')['communication_phone']);
      $par_data_person->set('communication_phone', $work_phone_preference_value);
      // Save the mobile phone preference.
      $mobile_phone_preference_value = isset($this->getTempDataValue('preferred_contact')['communication_mobile'])
        && !empty($this->getTempDataValue('preferred_contact')['communication_mobile']);
      $par_data_person->set('communication_mobile', $mobile_phone_preference_value);

      if ($par_data_person->save()) {
        // Only delete the form data for the par_partnership_contact form.
        $this->deleteFormTempData('par_partnership_contact');
      }
      else {
        $message = $this->t('This %person could not be saved for %form_id');
        $replacements = [
          '%person' => $this->getTempDataValue('name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }

  }

}
