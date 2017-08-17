<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionContactForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_partnership_primary_contact';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, $par_data_person = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }
    if ($par_data_person) {
      // Contact.
      $this->loadDataValue("salutation", $par_data_person->get('salutation')->getString());
      $this->loadDataValue("first_name", $par_data_person->get('first_name')->getString());
      $this->loadDataValue("last_name", $par_data_person->get('last_name')->getString());
      $this->loadDataValue("phone", $par_data_person->get('work_phone')->getString());
      $this->loadDataValue("mobile_phone", $par_data_person->get('mobile_phone')->getString());
      $this->loadDataValue("email", $par_data_person->get('email')->getString());
      $this->loadDataValue("notes", $par_data_person->get('communication_notes')->getString());

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => !empty($par_data_person->get('communication_email')->getString()) ? 'communication_email' : FALSE,
        'communication_phone' => !empty($par_data_person->get('communication_phone')->getString()) ? 'communication_phone' : FALSE,
        'communication_mobile' => !empty($par_data_person->get('communication_mobile')->getString()) ? 'communication_mobile' : FALSE,
      ];
      $this->loadDataValue('preferred_contact', $contact_options);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');

    //Leading paragraph
    $form['leading_paragraph'] = [
      '#type' => 'markup',
      '#markup' => t('<p>State who is the main contact for this business in your own primary authority team. Their contact information will be visible to anyone logging into the Primary Authority Register, including enforcement officers.</p>'),
    ];

    // The Person's title.
    $form['salutation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->getDefaultValues("salutation"),
    ];

    // The Person's name.
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#default_value' => $this->getDefaultValues("first_name"),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name'),
      '#default_value' => $this->getDefaultValues("last_name"),
    ];

    // The Person's work phone number.
    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Work phone'),
      '#default_value' => $this->getDefaultValues("phone"),
    ];

    // The Person's work phone number.
    $form['mobile_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile phone'),
      '#default_value' => $this->getDefaultValues("mobile_phone"),
    ];

    // The Person's work phone number.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#default_value' => $this->getDefaultValues("email"),
    ];

    // Preferred contact methods.
    $contact_options = [
      'communication_email' => $person_bundle->getBooleanFieldLabel('communication_email', 'on'),
      'communication_phone' => $person_bundle->getBooleanFieldLabel('communication_phone', 'on'),
      'communication_mobile' => $person_bundle->getBooleanFieldLabel('communication_mobile', 'on'),
    ];
    $form['preferred_contact'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Preferred method of contact'),
      '#options' => $contact_options,
      '#default_value' => $this->getDefaultValues("preferred_contact", []),
      '#return_value' => 'on',
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Contact notes (optional)'),
      '#default_value' => $this->getDefaultValues('notes'),
      '#description' => 'Add any additional notes about how best to contact this person.',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(4)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $previous_link]),
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
    // No validation yet.
    parent::validateForm($form, $form_state);
    $par_data_person = $this->getRouteParam('par_data_person');
    $form_items = [
      'salutation' => 'salutation',
      'first_name' => 'first_name',
      'last_name' => 'last_name',
      'work_phone' => 'work_phone',
      'email' => 'email',
    ];
    foreach($form_items as $element_item => $form_item) {
      $fields[$element_item] = [
        'value' => $form_state->getValue($form_item),
        'key' => $form_item,
        'tokens' => [
          '%field' => $form[$form_item]['#title']->render(),
        ],
      ];
    }

    $errors = $par_data_person->validateFields($fields);
    // Display error messages.
    foreach($errors as $field => $message) {
      $form_state->setErrorByName($field, $message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $par_data_person = $this->getRouteParam('par_data_person');
    $par_data_person->set('salutation', $this->getTempDataValue('salutation'));
    $par_data_person->set('first_name', $this->getTempDataValue('first_name'));
    $par_data_person->set('last_name', $this->getTempDataValue('last_name'));
    $par_data_person->set('work_phone', $this->getTempDataValue('work_phone'));
    $par_data_person->set('mobile_phone', $this->getTempDataValue('mobile_phone'));
    $par_data_person->set('email', $this->getTempDataValue('email'));
    $par_data_person->set('communication_notes', $this->getTempDataValue('notes'));

    // Save the email preference.
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
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %person could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getTempDataValue('name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(4), $this->getRouteParams());
  }

}
