<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsContactSuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  // @todo remove override.
//  protected $flow = 'partnership_authority';

  //  protected $formItems = [
  //    'par_data_person:person' => [
  //      'first_name' => 'first_name',
  //      'last_name' => 'last_name',
  //      'work_phone' => 'work_phone',
  //      'mobile_phone' => 'mobile_phone',
  //      'email' => 'email',
  //      // @todo will need to look into this further on the next piece of work.
  //      //  'communication_email'
  //      //  'communication_phone'
  //      //  'communication_mobile'
  //      //  'communication_notes' => 'notes'
  //    ],
  //  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_contact_suggestion';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
    // If we're editing an entity we should set the state
    // to something other than default to avoid conflicts
    // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

    //    if ($par_data_person) {
    //      // Contact.
    //      $this->loadDataValue("salutation", $par_data_person->get('salutation')->getString());
    //      $this->loadDataValue("first_name", $par_data_person->get('first_name')->getString());
    //      $this->loadDataValue("last_name", $par_data_person->get('last_name')->getString());
    //      $this->loadDataValue("phone", $par_data_person->get('work_phone')->getString());
    //      $this->loadDataValue("mobile_phone", $par_data_person->get('mobile_phone')->getString());
    //      $this->loadDataValue("email", $par_data_person->get('email')->getString());
    //      $this->loadDataValue("notes", $par_data_person->get('communication_notes')->getString());
    //
    //      // Get preferred contact methods.
    //      $contact_options = [
    //        'communication_email' => $par_data_person->retrieveBooleanValue('communication_email'),
    //        'communication_phone' => $par_data_person->retrieveBooleanValue('communication_phone'),
    //        'communication_mobile' => $par_data_person->retrieveBooleanValue('communication_mobile'),
    //      ];
    //
    //      // Checkboxes works nicely with keys, filtering booleans for "1" value.
    //      $this->loadDataValue('preferred_contact', array_keys($contact_options, 1));
    //    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $properties = [
      'first_name' => $this->getDefaultValues('first_name', '', 'par_partnership_contact'),
      'last_name' => $this->getDefaultValues('last_name', '', 'par_partnership_contact'),
      'email' => $this->getDefaultValues('email', '', 'par_partnership_contact'),
      'mobile_phone' => $this->getDefaultValues('mobile_phone', '', 'par_partnership_contact'),
      'work_phone' => $this->getDefaultValues('work_phone', '', 'par_partnership_contact'),
    ];

    $people = [];

    foreach ($properties as $property => $value) {
      $people += \Drupal::entityManager()
        ->getStorage('par_data_person')
        ->loadByProperties([$property => $value]);
    }

    $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

    foreach($people as $person) {
      $person_view = $person_view_builder->view($person, 'detailed');

      $people_options[$person->id()] = $this->renderMarkupField($person_view)['#markup'];
    }

    $people_options['new'] = 'No, I want to create a new user.';

    $form['option'] = [
      '#type' => 'radios',
      '#title' => t('Did you mean any of these users?'),
      '#options' => $people_options,
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => t('Save'),
    ];

    $cancel_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($person_view_builder);
    $this->addCacheableDependency($people);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get partnership entity from URL.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    // Now find the authority.
    $par_data_authority = current($par_data_partnership->getAuthority());

    if ($this->getTempDataValue('option') === 'new') {

      // Create new person entity.
       $par_data_person = ParDataPerson::create([
        'type' => 'person',
        'salutation' => $this->getTempDataValue('salutation', 'par_partnership_contact'),
        'first_name' => $this->getTempDataValue('first_name', 'par_partnership_contact'),
        'last_name' => $this->getTempDataValue('last_name', 'par_partnership_contact'),
        'work_phone' => $this->getTempDataValue('work_phone', 'par_partnership_contact'),
        'mobile_phone' => $this->getTempDataValue('mobile_phone', 'par_partnership_contact'),
        'email' => $this->getTempDataValue('email', 'par_partnership_contact'),
        'communication_notes' => $this->getTempDataValue('notes', 'par_partnership_contact')
      ]);

      // @todo refactor this to use $this->getTempDataBooleanValue() or similar.
      // Save the email preference.
      $email_preference_value = isset($this->getTempDataValue('preferred_contact')['communication_email'])
        && !empty($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_email']);
      $par_data_person->set('communication_email', $email_preference_value);
      // Save the work phone preference.
      $work_phone_preference_value = isset($this->getTempDataValue('preferred_contact')['communication_phone'])
        && !empty($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_phone']);
      $par_data_person->set('communication_phone', $work_phone_preference_value);
      // Save the mobile phone preference.
      $mobile_phone_preference_value = isset($this->getTempDataValue('preferred_contact')['communication_mobile'])
        && !empty($this->getTempDataValue('preferred_contact', 'par_partnership_contact')['communication_mobile']);
      $par_data_person->set('communication_mobile', $mobile_phone_preference_value);

    }
    else {

      $person_id = $this->getTempDataValue('option');

      if (isset($person_id) && is_numeric($person_id)) {
        $par_data_person = ParDataPerson::load($person_id);
      }

    }

    if (isset($par_data_person) && $par_data_person->save()) {

      // Add to field_authority_person.
      $par_data_partnership->get('field_authority_person')
        ->appendItem($par_data_person->id());

      // Update field_person on authority.
      $par_data_authority->get('field_person')
        ->appendItem($par_data_person->id());

    }

    if ($par_data_person->id() &&
      $par_data_partnership->save() &&
      $par_data_authority->save()) {
      $this->deleteStore();
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
