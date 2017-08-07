<?php

namespace Drupal\par_flow_transition_business\Form;

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
  protected $flow = 'transition_business';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_business_contact';
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
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

    if ($par_data_person) {
      // Contact.
      $this->loadDataValue("person_salutation", $par_data_person->get('salutation')->getString());
      $this->loadDataValue("person_first_name", $par_data_person->get('first_name')->getString());
      $this->loadDataValue("person_last_name", $par_data_person->get('last_name')->getString());
      $this->loadDataValue("person_phone", $par_data_person->get('work_phone')->getString());
      $this->loadDataValue("person_mobile_phone", $par_data_person->get('mobile_phone')->getString());
      $this->loadDataValue("person_email", $par_data_person->get('email')->getString());
      $this->loadDataValue("person_contact_method", $par_data_person->getPreferredCommunicationMethodId());
      $this->loadDataValue('person_id', $par_data_person->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_person);

    // The Person's title.
    if ($title_options = $par_data_person->getTitleOptions()) {
      $form['salutation'] = [
        '#type' => 'select',
        '#title' => $this->t('Title (optional)'),
        '#default_value' => $this->getDefaultValues("person_salutation"),
        '#options' => $title_options,
      ];
    }

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#default_value' => $this->getDefaultValues("person_first_name"),
      '#required' => TRUE,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => $this->getDefaultValues("person_last_name"),
      '#required' => TRUE,
    ];

    // The Person's work phone number.
    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Work Phone'),
      '#default_value' => $this->getDefaultValues("person_phone"),
      '#required' => TRUE,
    ];

    // The Person's work phone number.
    $form['mobile_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone (optional)'),
      '#default_value' => $this->getDefaultValues("person_mobile_phone"),
    ];

    // The Person's work phone number.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#default_value' => $this->getDefaultValues("person_email"),
      '#required' => TRUE,
    ];

    // Method of contact.
    $preferred_communications = $par_data_person->getPreferredCommunicationMethods();
    $form['contact_method'] = [
      '#type' => 'radios',
      '#title' => $this->t('Preferred method of contact (optional)'),
      '#default_value' => $this->getDefaultValues("person_contact_method"),
      '#options' => $preferred_communications,
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Contact notes (optional)'),
      '#default_value' => $this->getDefaultValues('notes'),
      '#description' => 'Add any additional notes about how best to contact this person.',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(4)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#prefix' => '<br>',
      '#markup' => t('@link', ['@link' => $previous_link]),
    ];
    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_person);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $person = $this->getRouteParam('par_data_person');

    $person->set('salutation', $this->getTempDataValue('salutation'));
    $person->set('first_name', $this->getTempDataValue('first_name'));
    $person->set('last_name', $this->getTempDataValue('last_name'));
    $person->set('work_phone', $this->getTempDataValue('work_phone'));
    $person->set('mobile_phone', $this->getTempDataValue('mobile_phone'));
    $person->set('email', $this->getTempDataValue('email'));
    $person->setPreferredCommunication($this->getTempDataValue('contact_method'));

    if ($person->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %person could not be saved for %form_id');
      $replacements = [
        '%person' => $this->getTempDataValue('person_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(4), $this->getRouteParams());
  }

}
