<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\user\Entity\User;

/**
 * The de-duping form.
 */
class ParPartnershipFlowsContactSuggestionForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * PAR Data Person ID.
   *
   * @var string
   */
  protected $par_data_person_id;

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Get files from "contact_form" step.
    // To use this form there must be a "form_data['contact_form']" key in the step configuration:
    // 1:
    //   route: example.route_name
    //   form_id: form_id_where_person_was_added
    //   form_data:
    //     upload: par_partnership_advice_upload_edit
    // 2:
    //   route: example.route_name_2
    //   form_id: example_form_id
    //   form_data:
    //     upload: form_id_where_person_was_added
    $cid = $this->getFlowNegotiator()->getFormKey('contact_form');
    $conditions = [
      'name' => [
        'AND' => [
          ['first_name', $this->getFlowDataHandler()->getDefaultValues('first_name', '', $cid), '='],
          ['last_name', $this->getFlowDataHandler()->getDefaultValues('last_name', '', $cid), '='],
        ],
      ],
      'email' => [
        'AND' => [
          ['email', $this->getFlowDataHandler()->getDefaultValues('email', '', $cid), '='],
        ],
      ],
      'mobile_phone' => [
        'OR' => [
          ['mobile_phone', $this->getFlowDataHandler()->getDefaultValues('mobile_phone', '', $cid), '='],
          ['work_phone', $this->getFlowDataHandler()->getDefaultValues('work_phone', '', $cid), '='],
        ],
      ],
    ];

    $people = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_person', $conditions, 10);

    $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

    $people_options = [];
    foreach($people as $person) {
      $person_view = $person_view_builder->view($person, 'detailed');

      $people_options[$person->id()] = $this->renderMarkupField($person_view)['#markup'];
    }

    $form['par_data_person_id'] = [
      '#type' => 'radios',
      '#title' => t('Did you mean any of these users?'),
      '#options' => $people_options + ['new' => 'No, it is not one of the above, create a new contact.'],
    ];

    // If no suggestions were found we want to automatically submit the form.
    if (count($people_options) === 0) {
      $this->getFlowDataHandler()->setTempDataValue('par_data_person_id', 'new');
      $this->submitForm($form, $form_state);

      // Pass param PAR Person created in the submit handler to the next step.
      return $this->redirect($this->getFlowNegotiator()->getFlow()->progressRoute('save'), $this->getRouteParams() + ['par_data_person' => $this->par_data_person_id]);
    }

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
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($this->getFlowDataHandler()->getDefaultValues('par_data_person_id') === 'new') {

      // Get files from "contact_form" step.
      // To use this form there must be a "form_data['contact_form']" key in the step configuration:
      // 1:
      //   route: example.route_name
      //   form_id: form_id_where_person_was_added
      //   form_data:
      //     upload: par_partnership_advice_upload_edit
      // 2:
      //   route: example.route_name_2
      //   form_id: example_form_id
      //   form_data:
      //     upload: form_id_where_person_was_added
      $cid = $this->getFlowNegotiator()->getFormKey('contact_form');
      $par_data_person = ParDataPerson::create([
        'type' => 'person',
        'salutation' => $this->getFlowDataHandler()->getTempDataValue('salutation', $cid),
        'first_name' => $this->getFlowDataHandler()->getTempDataValue('first_name', $cid),
        'last_name' => $this->getFlowDataHandler()->getTempDataValue('last_name', $cid),
        'work_phone' => $this->getFlowDataHandler()->getTempDataValue('work_phone', $cid),
        'mobile_phone' => $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $cid),
        'email' => $this->getFlowDataHandler()->getTempDataValue('email', $cid),
        'communication_notes' => $this->getFlowDataHandler()->getTempDataValue('notes', $cid)
      ]);

      // Save the email preference.
      $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_email'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_email']);
      $par_data_person->set('communication_email', $email_preference_value);
      // Save the work phone preference.
      $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_phone'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_phone']);
      $par_data_person->set('communication_phone', $work_phone_preference_value);
      // Save the mobile phone preference.
      $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_mobile'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $cid)['communication_mobile']);
      $par_data_person->set('communication_mobile', $mobile_phone_preference_value);

      $par_data_person->save();

    }
    else {
      $person_id = $this->getFlowDataHandler()->getDefaultValues('par_data_person_id');
      $par_data_person = ParDataPerson::load($person_id);

    }

    if ($par_data_person && $par_data_person->id()) {

      // Set route param for invite form.
      $this->par_data_person_id = $par_data_person->id();

      // Based on the flow we're in we also need to
      // Update field_person on authority or organisation.
      if ($this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
        // Add to field_authority_person.
        $par_data_partnership->get('field_authority_person')
          ->appendItem($par_data_person->id());

        // Add the person to the authority as well.
        $par_data_member_entity = current($par_data_partnership->getAuthority());
        $par_data_member_entity->get('field_person')
          ->appendItem($par_data_person->id());
      }
      else {
        // Add to field_organisation_person.
        $par_data_partnership->get('field_organisation_person')
          ->appendItem($par_data_person->id());

        // Add the person to the organisation as well.
        $par_data_member_entity = current($par_data_partnership->getOrganisation());
        $par_data_member_entity->get('field_person')
          ->appendItem($par_data_person->id());
      }
    }

    if ($par_data_person && $par_data_partnership->save() &&
        $par_data_member_entity->save()) {
      $this->getFlowDataHandler()->deleteStore();

      // Inject PAR Person we just created into the next step.
      $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->progressRoute('save'), $this->getRouteParams() + ['par_data_person' => $this->par_data_person_id]);
    }
    else {
      $message = $this->t('This %person could not be saved for %form_id');
      $replacements = [
        '%person' => $this->getFlowDataHandler()->getTempDataValue('last_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

  }

}
