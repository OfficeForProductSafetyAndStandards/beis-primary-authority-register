<?php

namespace Drupal\par_partnership_contact_update_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\invite\Entity\Invite;
use Drupal\invite\InviteConstants;
use Drupal\invite\InviteInterface;
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
use Drupal\par_partnership_contact_update_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_update_flows\ParFormCancelTrait;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Review contact information';

  /**
   * @return DateFormatterInterface
   */
  protected function getEntityTypeManager() {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataPerson $par_data_person */
    /** @var User $account */
    /** @var ParDataAuthority $par_data_authority */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var Invite $invite */
    $type = $this->getFlowDataHandler()->getParameter('type');

    if (isset($par_data_person)) {
      $this->getFlowDataHandler()
        ->setFormPermValue("full_name", $par_data_person->getFullName());
      $this->getFlowDataHandler()
        ->setFormPermValue("work_phone", $par_data_person->getWorkPhone());
      $this->getFlowDataHandler()
        ->setFormPermValue("mobile_phone", $par_data_person->getMobilePhone());
      $this->getFlowDataHandler()
        ->setFormPermValue("email", $par_data_person->getEmailWithPreferences());
    }

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('role', NULL, $cid_role_select);

    if (isset($account)) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'existing');
    }
    elseif (isset($invite) && (int) $invite->getStatus() === InviteConstants::INVITE_VALID && $invite->id()) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'active_invite');
    }
    elseif (isset($invite)) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'invited');
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
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('full_name', '(none)'),
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
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('email', '(none)'),
        ]
      ],
      'work_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Work phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('work_phone', '(none)'),
        ]
      ],
      'mobile_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Mobile phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('mobile_phone', '(none)'),
        ]
      ],
    ];

    switch ($this->getFlowDataHandler()->getDefaultValues("user_status", NULL)) {
      case 'existing':

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "A user account already exists for this person.",
          ],
        ];

        break;

      case 'invited':

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "An invitation will be sent to this person to invite them to join the Primary Authority Register.",
          ],
        ];

        break;

      case 'active_invite':
      default:

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "An invitation has already been sent to this person to join the Primary Authority Register.",
          ],
        ];
    }

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $current_user = $this->getCurrentUser();

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_add_contact');
    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('invite');
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');

    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = ParChooseAccount::getUserAccount($account_selection);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    if ($par_data_person) {
      // Store the original email to check if it changes.
      $this->getFlowDataHandler()->setFormPermValue('orignal_email', $par_data_person->getEmail());

      // Update the person record with the new values.
      $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid));
      $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid));
      $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid));
      $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid));
      $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid));
      $par_data_person->updateEmail($this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid), $current_user);

      if ($communication_notes = $this->getFlowDataHandler()->getTempDataValue('notes', $contact_details_cid)) {
        $par_data_person->set('communication_notes', $communication_notes);
      }

      if ($preferred_contact = $this->getFlowDataHandler()->getTempDataValue('preferred_contact', $contact_details_cid)) {
        $email_preference_value = isset($preferred_contact['communication_email']) && !empty($preferred_contact['communication_email']);
        $par_data_person->set('communication_email', $email_preference_value);

        // Save the work phone preference.
        $work_phone_preference_value = isset($preferred_contact['communication_phone']) && !empty($preferred_contact['communication_phone']);
        $par_data_person->set('communication_phone', $work_phone_preference_value);

        // Save the mobile phone preference.
        $mobile_phone_preference_value = isset($preferred_contact['communication_mobile']) && !empty($preferred_contact['communication_mobile']);
        $par_data_person->set('communication_mobile', $mobile_phone_preference_value);
      }
    }

    $role = $this->getFlowDataHandler()->getDefaultValues('role', NULL, $cid_role_select);
    if (!$account) {
      switch ($role) {
        case 'par_authority':
          $invitation_type = 'invite_authority_member';

          break;

        case 'par_organisation':
          $invitation_type = 'invite_organisation_member';

          break;
      }
    }
    // Create invitation if an invitation type has been set and no existing user has been found.
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    if (isset($invitation_type) && $account_selection === ParChooseAccount::CREATE) {
      if ($email = $this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid)) {
        $invitations = $this->getEntityTypeManager()
          ->getStorage('invite')
          ->loadByProperties([
            'field_invite_email_address' => $email,
            'status' => InviteConstants::INVITE_VALID
          ]);
      }

      // Only create a new invitation if one doesn't already exist.
      if (isset($invitations) && count($invitations) >= 1) {
        $invite = current($invitations);
      }
      else {
        $invite = Invite::create([
          'type' => $invitation_type,
          'user_id' => $this->getCurrentUser()->id(),
          'invitee' => $this->getFlowDataHandler()
            ->getDefaultValues('to', NULL, $cid_invitation),
        ]);
        $invite->set('field_invite_email_address', $this->getFlowDataHandler()
          ->getDefaultValues('to', NULL, $cid_invitation));
        $invite->set('field_invite_email_subject', $this->getFlowDataHandler()
          ->getDefaultValues('subject', NULL, $cid_invitation));
        $invite->set('field_invite_email_body', $this->getFlowDataHandler()
          ->getDefaultValues('body', NULL, $cid_invitation));
        $invite->setPlugin('invite_by_email');
      }

      //@TODO If the email address has been updated we may also need to consider recinding
      // invitations to the old email address (but only if it is not attached to another
      // par_data_person contact record).
    }
    // Update the user account.
    elseif ($account) {
      // If there is an existing user attach it to this person.
      $par_data_person->setUserAccount($account);

      // Update any roles this user may not already have.
      if ($role && !$account->hasRole($role)) {
        $account->addRole($role);
      }
    }

    $entities = [
      'par_data_partnership' => $par_data_partnership,
      'par_data_person' => $par_data_person,
      'account' => $account ?: NULL,
    ];

    if (isset($invite) && $invite instanceof InviteInterface) {
      $entities['invite'] = $invite;
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPartnership $par_data_partnership */
    /** @var ParDataPerson $par_data_person */
    /** @var User $account */
    /** @var Invite $invite */
    $type = $this->getFlowDataHandler()->getParameter('type');

    if ($par_data_person->save()) {
      // Re-save the user account to store any roles that might have been added,
      // but also to clear the user account caches.
      if (isset($account)) {
        $account->save();
        \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);
      }
      // Send the invite.
      elseif (isset($invite)) {
        $invite->save();
      }

      // We also need to clear the relationships caches once
      // any new relationships have been saved.
      $par_data_person->getRelationships(NULL, NULL, TRUE);

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Person could not be created for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

    // Go to cancel route.
    switch ($type) {
      case 'organisation':
        $cancel_route = 'par_partnership_flows.organisation_details';

        break;

      case 'authority':
        $cancel_route = 'par_partnership_flows.authority_details';

        break;
    }

    if ($cancel_route) {
      $form_state->setRedirect($cancel_route, ['par_data_partnership' => $par_data_partnership->id()]);
    }
  }

}
