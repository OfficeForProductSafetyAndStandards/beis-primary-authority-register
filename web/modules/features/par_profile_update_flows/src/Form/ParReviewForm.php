<?php

namespace Drupal\par_profile_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_profile_update_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Profile review';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $cid_person_select = $this->getFlowNegotiator()->getFormKey('par_choose_person');
    $person = $this->getFlowDataHandler()->getDefaultValues('user_person', '', $cid_person_select);
    if ($par_data_person = ParDataPerson::load($person)) {
      $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPerson $par_data_person */

    $form['personal'] = [
      '#type' => 'fieldset',
      'name' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Name',
        [
          '#markup' => $par_data_person->getFullName()
        ]
      ],
    ];

    $form['contact_details'] = [
      '#type' => 'fieldset',
      'email' => [
        '#type' => 'fieldset',
        '#title' => 'Email',
        '#attributes' => ['class' => 'form-group'],
        [
          '#markup' => $par_data_person->getEmail()
        ]
      ],
      'work_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Work phone',
        [
          '#markup' => $par_data_person->getWorkPhone()
        ]
      ],
      'mobile_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Work phone',
        [
          '#markup' => $par_data_person->getMobilePhone()
        ]
      ],
      'communication_noes' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Communication notes',
        0 => $par_data_person->get('communication_notes')->isEmpty() ? ['#markup' => '(none)'] : $par_data_person->communication_notes->view('full'),
      ],

    ];

    $account = $this->getFlowDataHandler()->getParameter('user');
    if ($account && $account->isAuthenticated() && $people = $this->getParDataManager()->getUserPeople($account)) {
      if (count($people) > 1) {
        $form['update_all_contacts'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Would you like to update all contact records with this information?'),
          '#default_value' => TRUE,
          '#return_value' => 'on',
        ];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_profile_update');
    $contact_preferences_cid = $this->getFlowNegotiator()->getFormKey('par_preferences_update');

    if ($par_data_person) {
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid));
      $par_data_person->set('email', $this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid));

      $par_data_person->set('communication_notes', $this->getFlowDataHandler()->getTempDataValue('notes', $contact_preferences_cid));

      $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_preferences_cid)['communication_email'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_preferences_cid)['communication_email']);
      $par_data_person->set('communication_email', $email_preference_value);
      // Save the work phone preference.
      $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_preferences_cid)['communication_phone'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_preferences_cid)['communication_phone']);
      $par_data_person->set('communication_phone', $work_phone_preference_value);
      // Save the mobile phone preference.
      $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_preferences_cid)['communication_mobile'])
        && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_preferences_cid)['communication_mobile']);
      $par_data_person->set('communication_mobile', $mobile_phone_preference_value);
    }

    return [
      'par_data_person' => $par_data_person,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPerson $par_data_person */

    if ($par_data_person->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('User profile could not be updated for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
