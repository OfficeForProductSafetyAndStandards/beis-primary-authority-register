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

      // Set the data values on the entities
      $entities = $this->createEntities();
      extract($entities);
      /** @var ParDataPerson $par_data_person */
      /** @var User $account */

      $this->getFlowDataHandler()->setFormPermValue("full_name", $par_data_person->getFullName());
      $this->getFlowDataHandler()->setFormPermValue("work_phone", $par_data_person->getWorkPhone());
      $this->getFlowDataHandler()->setFormPermValue("mobile_phone", $par_data_person->getMobilePhone());
      $this->getFlowDataHandler()->setFormPermValue("email", $par_data_person->getEmailWithPreferences());
      if (!$par_data_person->get('communication_notes')->isEmpty()) {
        $communication_notes = $par_data_person->communication_notes->view('full');
        $this->getFlowDataHandler()->setFormPermValue("communication_notes", $communication_notes);
      }

      if ($account && $account->isAuthenticated() && $people = $this->getParDataManager()->getUserPeople($account)) {
        if (count($people) > 1) {
          $this->getFlowDataHandler()->setFormPermValue("multiple_people", TRUE);
        }
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['personal'] = [
      '#type' => 'fieldset',
      'name' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Name',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('full_name', ''),
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
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('email', ''),
        ]
      ],
      'work_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Work phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('work_phone', ''),
        ]
      ],
      'mobile_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Mobile phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('mobile_phone', ''),
        ]
      ],
      'communication_noes' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Communication notes',
        0 => $this->getFlowDataHandler()->getDefaultValues('communication_notes', ['#markup' => '(none)']),
      ],

    ];

    if ($this->getFlowDataHandler()->getFormPermValue("multiple_people")) {
      $form['update_all_contacts'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Would you like to update all contact records with this information?'),
        '#default_value' => TRUE,
        '#return_value' => 'on',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $account = $this->getFlowDataHandler()->getParameter('user');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_profile_update');
    $contact_preferences_cid = $this->getFlowNegotiator()->getFormKey('par_preferences_update');

    if ($par_data_person) {
      // Store the original email to check if it changes.
      $this->getFlowDataHandler()->setFormPermValue('orginal_email', $par_data_person->getEmail());

      // Update the person record with the new values.
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid));
      $par_data_person->updateEmail($this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid), $account);

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
      'account' => $account,
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
    /** @var User $account */

    // Merge all accounts or just save the person straight up.
    if (($this->getFlowDataHandler()->getTempDataValue('update_all_contacts') === 'on' && $par_data_person->mergePeople())
        || $par_data_person->save()) {
      // Also save the user if the email has been updated.
      if ($account->getEmail() !== $this->getFlowDataHandler()->getFormPermValue('orginal_email')) {
        $account->save();
        $this->getParDataManager()->getMessenger()->addMessage(t('The email address you use to login has been updated to @email', ['@email' => $account->getEmail()]));
      }

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
