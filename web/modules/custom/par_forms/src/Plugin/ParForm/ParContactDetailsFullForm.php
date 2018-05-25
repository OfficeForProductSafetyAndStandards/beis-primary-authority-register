<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact details form plugin.
 *
 * @ParForm(
 *   id = "contact_details_full",
 *   title = @Translation("Contact details full form.")
 * )
 */
class ParContactDetailsFullForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_person:person' => [
      'first_name' => 'first_name',
      'last_name' => 'last_name',
      'work_phone' => 'work_phone',
      'mobile_phone' => 'mobile_phone',
      'email' => 'email',
      'communication_notes' => 'notes',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->setDefaultValuesByKey("salutation", $cardinality, $par_data_person->get('salutation')->getString());
      $this->setDefaultValuesByKey("first_name", $cardinality, $par_data_person->get('first_name')->getString());
      $this->setDefaultValuesByKey("last_name", $cardinality, $par_data_person->get('last_name')->getString());
      $this->setDefaultValuesByKey("work_phone", $cardinality, $par_data_person->get('work_phone')->getString());
      $this->setDefaultValuesByKey("mobile_phone", $cardinality, $par_data_person->get('mobile_phone')->getString());
      $this->setDefaultValuesByKey("email", $cardinality, $par_data_person->get('email')->getString());
      $this->setDefaultValuesByKey("notes", $cardinality, $par_data_person->get('communication_notes')->getString());

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => $par_data_person->getBoolean('communication_email'),
        'communication_phone' => $par_data_person->getBoolean('communication_phone'),
        'communication_mobile' => $par_data_person->getBoolean('communication_mobile'),
      ];

      // Checkboxes works nicely with keys, filtering booleans for "1" value.
      $this->setDefaultValuesByKey('preferred_contact', $cardinality, array_keys($contact_options, 1));
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $form['salutation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the title (optional)'),
      '#description' => $this->t('For example, Ms Mr Mrs Dr'),
      '#default_value' => $this->getDefaultValuesByKey('salutation', $cardinality),
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the first name'),
      '#default_value' => $this->getDefaultValuesByKey('first_name', $cardinality),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the last name'),
      '#default_value' => $this->getDefaultValuesByKey('last_name', $cardinality),
    ];

    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the work phone number'),
      '#default_value' => $this->getDefaultValuesByKey('work_phone', $cardinality),
    ];

    $form['mobile_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the mobile phone number (optional)'),
      '#default_value' => $this->getDefaultValuesByKey('mobile_phone', $cardinality),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Enter the email address'),
      '#default_value' => $this->getDefaultValuesByKey('email', $cardinality),
      // Prevent modifying email if editing an existing user.
      '#disabled' => !empty($par_data_person),
    ];

    // Get preferred contact methods labels.
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $contact_options = [
      'communication_email' => $person_bundle->getBooleanFieldLabel('communication_email', 'on'),
      'communication_phone' => $person_bundle->getBooleanFieldLabel('communication_phone', 'on'),
      'communication_mobile' => $person_bundle->getBooleanFieldLabel('communication_mobile', 'on'),
    ];

    $form['preferred_contact'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the preferred methods of contact (optional)'),
      '#options' => $contact_options,
      '#default_value' => $this->getDefaultValuesByKey('preferred_contact', $cardinality, []),
      '#return_value' => 'on',
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide contact notes (optional)'),
      '#default_value' => $this->getDefaultValuesByKey('notes', $cardinality),
      '#description' => 'Add any additional notes about how best to contact this person.',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(&$form_state, $cardinality = 1, array $violations = []) {
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

    return parent::validate($form_state, $cardinality);
  }
}
