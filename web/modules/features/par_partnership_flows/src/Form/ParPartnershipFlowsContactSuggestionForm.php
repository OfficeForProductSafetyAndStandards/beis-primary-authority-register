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
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $properties = [
      'name' => [
        'first_name' => $this->getDefaultValues('first_name', '', 'par_partnership_contact'),
        'last_name' => $this->getDefaultValues('last_name', '', 'par_partnership_contact'),
      ],
      'email' => [
        'email' => $this->getDefaultValues('email', '', 'par_partnership_contact'),
      ],
      'mobile' => [
        'mobile_phone' => $this->getDefaultValues('mobile_phone', '', 'par_partnership_contact'),
      ],
      'phone' => [
        'work_phone' => $this->getDefaultValues('work_phone', '', 'par_partnership_contact'),
      ],
    ];

    $people = [];
    foreach ($properties as $group => $conditions) {
      $people += \Drupal::entityManager()
        ->getStorage('par_data_person')
        ->loadByProperties($conditions);
    }


    $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

    $people_options = [];
    foreach($people as $person) {
      $person_view = $person_view_builder->view($person, 'detailed');

      $people_options[$person->id()] = $this->renderMarkupField($person_view)['#markup'];
    }

    // If no suggestions were found we want to automatically submit the form.
    if (count($people_options) <= 0) {
      $this->setTempDataValue('option', 'new');
      $this->submitForm($form, $form_state);
      return $this->redirect($this->getFlow()->getNextRoute('save'), $this->getRouteParams());
    }

    $people_options['new'] = 'No, I want to create a new user.';

    $form['option'] = [
      '#type' => 'radios',
      '#title' => t('Did you mean any of these users?'),
      '#options' => $people_options,
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
      // Based on the flow we're in we also need to
      // Update field_person on authority or organisation.
      if ($this->getFlowName() === 'partnership_authority') {
        // Add to field_authority_person.
        $par_data_partnership->get('field_authority_person')
          ->appendItem($par_data_person->id());

        // Add the person to the authority as well.
        $par_data_member_entity = current($par_data_partnership->get('field_authority')->referencedEntities());
        $par_data_member_entity->get('field_person')
          ->appendItem($par_data_person->id());
      }
      else {
        // Add to field_organisation_person.
        $par_data_partnership->get('field_organisation_person')
          ->appendItem($par_data_person->id());

        // Add the person to the organisation as well.
        $par_data_member_entity = current($par_data_partnership->get('field_organisation')->referencedEntities());
        $par_data_member_entity->get('field_person')
          ->appendItem($par_data_person->id());
      }
    }

    if ($par_data_person->id() &&
      $par_data_partnership->save() &&
      $par_data_member_entity->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %person could not be saved for %form_id');
      $replacements = [
        '%person' => $this->getTempDataValue('last_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

  }

}
