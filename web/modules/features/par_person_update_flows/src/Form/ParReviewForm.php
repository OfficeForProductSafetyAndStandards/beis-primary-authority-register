<?php

namespace Drupal\par_person_update_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_person_update_flows\ParFlowAccessTrait;
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
    // Set the user account that is being updated as a parameter for plugins to access
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = ParChooseAccount::getUserAccount($account_selection);

    if ($account) {
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      // Set the data values on the entities
      $entities = $this->createEntities();
      extract($entities);
      /** @var ParDataPerson $par_data_person */
      /** @var User $account */
      /** @var ParDataAuthority[] $par_data_authority */
      /** @var ParDataOrganisation[] $par_data_organisation */

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

      if ($par_data_authority) {
        $authority_names = $this->getParDataManager()->getEntitiesAsOptions($par_data_authority, []);
        $this->getFlowDataHandler()->setFormPermValue("authorities", implode('<br>', $authority_names));
      }
      if ($par_data_organisation) {
        $organisation_names = $this->getParDataManager()->getEntitiesAsOptions($par_data_organisation, []);
        $this->getFlowDataHandler()->setFormPermValue("organisations", implode('<br>', $organisation_names));
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

    $form['memberships'] = [
      '#type' => 'fieldset',
    ];
    if ($authorities = $this->getFlowDataHandler()->getDefaultValues('authorities', NULL)) {
      $form['memberships']['authorities'] = [
        '#type' => 'fieldset',
        '#title' => 'Belongs to the following authorities',
        '#attributes' => ['class' => 'form-group'],
        [
          '#markup' => $authorities,
        ]
      ];
    }
    if ($organisations = $this->getFlowDataHandler()->getDefaultValues('organisations', NULL)) {
      $form['memberships']['organisations'] = [
        '#type' => 'fieldset',
        '#title' => 'Belongs to the following organisations',
        '#attributes' => ['class' => 'form-group'],
        [
          '#markup' => $organisations,
        ]
      ];
    }

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
    $current_user = $this->getCurrentUser();

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_person_update');
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('par_update_institution');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('par_update_institution');
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');

    $account = $this->getFlowDataHandler()->getParameter('user');

    if ($par_data_person) {
      // Store the original email to check if it changes.
      $this->getFlowDataHandler()->setFormPermValue('orginal_email', $par_data_person->getEmail());

      // Update the person record with the new values.
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid));
      $par_data_person->updateEmail($this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid), $current_user);

      // Make sure to save the related user account.
      if ($account) {
        $par_data_person->setUserAccount($account);
      }
    }

    // Get the authorities and organisations that will be associated with the person.
    $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_authority_cid);
    $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_organisation_cid);
    $par_data_authorities = $par_data_person->updateAuthorityMemberships($authority_ids);
    $par_data_organisations = $par_data_person->updateOrganisationMemberships($organisation_ids);

    return [
      'par_data_person' => $par_data_person,
      'account' => $account,
      'par_data_authority' => !empty($par_data_authorities) ? $par_data_authorities : NULL,
      'par_data_organisation' => !empty($par_data_organisations) ? $par_data_organisations : NULL,
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
    /** @var ParDataAuthority[] $par_data_authority */
    /** @var ParDataOrganisation[] $par_data_organisation */

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('par_update_institution');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('par_update_institution');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('invite');
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');

    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);

    $role = $this->getFlowDataHandler()->getDefaultValues('role', NULL, $cid_role_select);
    switch ($role) {
      case 'par_enforcement':
        $invitation_type = 'invite_enforcement_officer';

        break;

      case 'par_authority':
        $invitation_type = 'invite_authority_member';

        break;

      case 'par_organisation':
        $invitation_type = 'invite_organisation_member';

        break;

      case 'par_helpdesk':
        $invitation_type = 'invite_processing_team_member';

        break;
    }

    // Create invitation if an invitation type has been set and no existing user has been found.
    if (isset($invitation_type) && !$account && $account_selection === ParChooseAccount::CREATE) {
      $invite = Invite::create([
        'type' => $invitation_type,
        'user_id' => $this->getCurrentUser()->id(),
        'invitee' => $this->getFlowDataHandler()->getDefaultValues('to', NULL, $cid_invitation),
      ]);
      $invite->set('field_invite_email_address', $this->getFlowDataHandler()->getDefaultValues('to', NULL, $cid_invitation));
      $invite->set('field_invite_email_subject', $this->getFlowDataHandler()->getDefaultValues('subject', NULL, $cid_invitation));
      $invite->set('field_invite_email_body', $this->getFlowDataHandler()->getDefaultValues('body', NULL, $cid_invitation));
      $invite->setPlugin('invite_by_email');
    }
    // Update any roles as necessary.
    else if ($account && !$account->hasRole($role)) {
      $account->addRole($role);
    }

    // Merge all accounts (and save them) or just save the person straight up.
    if (($this->getFlowDataHandler()->getTempDataValue('update_all_contacts') === 'on' && $par_data_person->mergePeople())
        || $par_data_person->save()) {
      // Also save the user if the email has been updated.
      if ($account && $account->getEmail() !== $this->getFlowDataHandler()->getFormPermValue('orginal_email')) {
        $account->save();
        $this->getParDataManager()->getMessenger()->addMessage(t('The email address you use to login has been updated to @email', ['@email' => $account->getEmail()]));
      }

      // Update the membership authorities or organisations.
      $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_authority_cid);
      if ($authority_ids && (in_array($role, ['par_authority', 'par_enforcement']) || !$role)) {
        $par_data_person->updateAuthorityMemberships($authority_ids, TRUE);
      }
      $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_organisation_cid);
      if ($organisation_ids && ($role === 'par_organisation' || !$role)) {
        $par_data_person->updateOrganisationMemberships($organisation_ids, TRUE);
      }

      // Send the invite.
      if (isset($invite)) {
        $invite->save();
      }

      // We also need to clear the relationships caches once
      // any new relationships have been saved.
      $par_data_person->getRelationships(NULL, NULL, TRUE);
      
      // Also invalidate the user account cache if there is one.
      if ($account) {
        \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);
      }

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Person could not be updated for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
