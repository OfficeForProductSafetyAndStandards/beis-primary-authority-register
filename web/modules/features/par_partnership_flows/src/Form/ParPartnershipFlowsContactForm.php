<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
//use Drupal\Core\Session\AccountInterface;
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
        'communication_email' => $par_data_person->retrieveBooleanValue('communication_email'),
        'communication_phone' => $par_data_person->retrieveBooleanValue('communication_phone'),
        'communication_mobile' => $par_data_person->retrieveBooleanValue('communication_mobile'),
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

    // The Person's title.
    $form['salutation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->getDefaultValues("salutation"),
    ];

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
      '#default_value' => $this->getDefaultValues("work_phone"),
    ];

    // The Person's work phone number.
    $form['mobile_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile phone (optional)'),
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

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => $this->t($par_data_partnership ? 'Save' : 'Continue'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#name' => 'cancel',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::cancelForm'],
      '#attributes' => [
        'class' => ['btn-link']
      ],
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_person);
    $this->addCacheableDependency($person_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save contact.
    $par_data_person = $this->getRouteParam('par_data_person');

    // Create new person.
    if (!$par_data_person) {
      $par_data_person = \Drupal\par_data\Entity\ParDataPerson::create([
        'type' => 'person',
        'uid' => 1,
      ]);
    }

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
        // only delete the form data for the par_partnership_contact form.
        $this->deleteFormTempData('par_partnership_contact');
      } else {
        $message = $this->t('This %person could not be saved for %form_id');
        $replacements = [
          '%person' => $this->getTempDataValue('name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

    // @todo this needs to be somewhere else.
    if ($this->getFlowName() == 'partnership_application') {

      // Load the Authority.
      $par_data_authority = ParDataAuthority::load($this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection'));

      // @todo see possibility of injecting AccountProxy
      $user = User::load(\Drupal::currentUser()->id());

      // @todo ensure this returns somebody.
      $authority_user = ParDataPerson::load($this->parDataManager->getUserPerson($user, $par_data_authority)[0]);

      $organisation_id = $this->getDefaultValues('par_data_organisation_id','', 'par_partnership_organisation_suggestion');

      if ($organisation_id === 'new') {
        $par_data_premises = \Drupal\par_data\Entity\ParDataPremises::create([
          'type' => 'premises',
          // 'name' => $this->getTempDataValue('salutation'),
          'uid' => \Drupal::currentUser()->id(),
          'address' => [
            'country_code' => $this->getDefaultValues('country_code', '', 'par_partnership_address'),
            'address_line1' => $this->getDefaultValues('address_line1','', 'par_partnership_address'),
            'address_line2' => $this->getDefaultValues('address_line2','', 'par_partnership_address'),
            'locality' => $this->getDefaultValues('town_city','', 'par_partnership_address'),
            'administrative_area' => $this->getDefaultValues('county','', 'par_partnership_address'),
            'postal_code' => $this->getDefaultValues('postcode','', 'par_partnership_address'),
          ],
          'nation' => 'GB-SCT',
        ]);
        $par_data_premises->save();

        $par_data_organisation = \Drupal\par_data\Entity\ParDataOrganisation::create([
          'type' => 'organisation',
          'name' => $this->getDefaultValues('organisation_name','', 'par_partnership_application_organisation_search'),
          'uid' => \Drupal::currentUser()->id(),
          'organisation_name' => $this->getDefaultValues('organisation_name','', 'par_partnership_application_organisation_search'),
          //        'size' => 'Enormous',
          //        'employees_band' => 50,
          'nation' => 'GB-SCT',
          //        'comments' => 'ABCD Mart is a department store featured in the Sesame Street direct-to-video special Big Bird Gets Lost.',
          'premises_mapped' => TRUE,
          //        'trading_name' => [
          //          'ABCD',
          //          'ABCD Mart',
          //        ],
          'field_person' => [
            $par_data_person->id(),
          ],
          'field_premises' => [
            $par_data_premises->id(),
          ],
          //        'field_legal_entity' => [
          //          $legal_entity_1->id()
          //        ],
          //        'field_sic_code' => [
          //          $sic_code_1->save(),
          //        ]
        ]);

        $par_data_organisation->save();
      } else {
        $par_data_organisation = ParDataOrganisation::load($organisation_id);
      }

      // Save partnership.
      $partnership = \Drupal\par_data\Entity\ParDataPartnership::create([
        'type' => 'partnership',
        'name' => 'Partnership Name?',
        'uid' => \Drupal::currentUser()->id(),
        'partnership_type' => $this->getDefaultValues('application_type', '', 'par_partnership_application_type'),
        'partnership_status' => 'application',
        'about_partnership' => $this->getDefaultValues('about_partnership', '', 'par_partnership_about'),
        'terms_authority_agreed' => 1,
//        'communication_email' => TRUE,
//        'communication_phone' => TRUE,
//        'communication_notes' => '',
//        'approved_date' => '',
//        'expertise_details' => '',
//        'cost_recovery' => 'Cost recovered by Jo Smith',
//        'reject_comment' => '',
//        'revocation_source' => 'An RD Executive called Sue',
//        'revocation_date' => '2017-07-01',
//        'revocation_reason' => 'I saw a rat in Charlie\'s Cafe.',
//        'authority_change_comment' => '',
//        'organisation_change_comment' => '',
        'field_authority' => [
          $par_data_authority->id(),
        ],
        'field_organisation' => [
          $par_data_organisation->id(),
        ],
        'field_authority_person' => [
          $authority_user->id(),
        ],
        'field_organisation_person' => [
          $par_data_person->id(),
        ],
      ]);
      $partnership->save();

      var_dump($partnership->id());
      die();

    }

  }

}
