<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionSecondForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_partnership_details';

  public function getFormId() {
    return 'par_flow_transition_partnership_primary_contact';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataAuthority $par_data_authority
   *   The Authority being retrieved.
   * @param ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL, $par_data_person = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }
    if ($par_data_person) {
      // Contact.
      $this->loadDataValue("person_{$par_data_person->id()}_salutation", $par_data_person->get('salutation')->getString());
      $this->loadDataValue("person_{$par_data_person->id()}_name", $par_data_person->get('person_name')->getString());
      $this->loadDataValue("person_{$par_data_person->id()}_role", $par_data_person->get('role')->getString());
      $this->loadDataValue("person_{$par_data_person->id()}_phone", $par_data_person->get('work_phone')->getString());
      $this->loadDataValue("person_{$par_data_person->id()}_mobile_phone", $par_data_person->get('mobile_phone')->getString());
      $this->loadDataValue("person_{$par_data_person->id()}_email", $par_data_person->get('email')->getString());
    }
    $this->loadDataValue('person_id', $par_data_person->id());
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL, ParDataPerson $par_data_person = NULL) {
    $this->retrieveEditableValues($par_data_authority, $par_data_partnership, $par_data_person);

    // The Person's title.
    $title_options = [
      'Ms',
      'Mrs',
      'Mr',
      'Dr',
    ];
    $form['salutation'] = [
      '#type' => 'select',
      '#title' => $this->t('Title'),
      '#default_value' => $this->getDefaultValues("person_{$this->getDefaultValues('person_id')}_salutation"),
      '#options' => array_combine($title_options, $title_options),
      '#required' => TRUE,
    ];

    // The Person's name.
    $form['person_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $this->getDefaultValues("person_{$this->getDefaultValues('person_id')}_name"),
      '#required' => TRUE,
    ];

    // The Person's role.
    $form['person_role'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Role'),
      '#default_value' => $this->getDefaultValues("person_{$this->getDefaultValues('person_id')}_role"),
      '#required' => TRUE,
    ];

    // The Person's work phone number.
    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Work Phone'),
      '#default_value' => $this->getDefaultValues("person_{$this->getDefaultValues('person_id')}_phone"),
      '#required' => TRUE,
    ];

    // The Person's work phone number.
    $form['mobile_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone'),
      '#default_value' => $this->getDefaultValues("person_{$this->getDefaultValues('person_id')}_mobile_phone"),
      '#required' => TRUE,
    ];

    // The Person's work phone number.
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#default_value' => $this->getDefaultValues("person_{$this->getDefaultValues('person_id')}_email"),
      '#required' => TRUE,
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $person = $this->getRouteParam('par_data_person');
    $person->set('salutation', $this->getTempDataValue('salutation'));
    $person->set('person_name', $this->getTempDataValue('person_name'));
    $person->set('work_phone', $this->getTempDataValue('work_phone'));
    $person->set('mobile_phone', $this->getTempDataValue('mobile_phone'));
    $person->set('email', $this->getTempDataValue('email'));
    if ($person->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %person could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getTempDataValue('person_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect('par_flow_transition_partnership_details.overview', $this->getRouteParams());
  }
}
