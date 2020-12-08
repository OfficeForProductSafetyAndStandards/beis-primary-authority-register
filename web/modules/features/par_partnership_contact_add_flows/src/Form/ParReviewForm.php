<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\invite\Entity\Invite;
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
use Drupal\par_forms\Plugin\ParForm\ParDedupePersonForm;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Review contact information';

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

    $this->getFlowDataHandler()->setFormPermValue("institution", $type);
    if ($par_data_partnership) {
      $this->getFlowDataHandler()
        ->setFormPermValue("partnership", lcfirst($par_data_partnership->label()));
    }

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

    if (isset($account)) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'existing');
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
    $form['partnership'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      [
        '#markup' => $this->t('The following person will be added to the @partnership.',
          ['@partnership' => $this->getFlowDataHandler()->getFormPermValue("partnership")]),
      ],
    ];

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

      case 'none':
      default:

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "A user account will not be created for this person.",
          ],
        ];
    }

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['save', 'cancel']);

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_add_contact');
    $contact_dedupe_cid = $this->getFlowNegotiator()->getFormKey('dedupe_contact');
    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('invite');
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');

    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = ParChooseAccount::getUserAccount($account_selection);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $type = $this->getFlowDataHandler()->getParameter('type');

    $deduped_contact = $this->getFlowDataHandler()->getDefaultValues('contact_record', NULL, $contact_dedupe_cid);
    $par_data_person = ParDedupePersonForm::getDedupedPerson($deduped_contact);

    // Create the new person.
    if (!$par_data_person) {
      $par_data_person = ParDataPerson::create([
        'type' => 'person',
        'salutation' => $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid),
        'first_name' => $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid),
        'last_name' => $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid),
        'work_phone' => $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid),
        'mobile_phone' => $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid),
      ]);
      $par_data_person->updateEmail($this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid), $account);

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

    switch ($type) {
      case 'authority':
        $entities['par_data_authority'] = $par_data_partnership->getAuthority(TRUE);

        break;

      case 'organisation':
        $entities['par_data_organisation'] = $par_data_partnership->getOrganisation(TRUE);

        break;

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
    /** @var ParDataAuthority $par_data_authority */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var Invite $invite */
    $type = $this->getFlowDataHandler()->getParameter('type');

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('par_add_institution');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('par_add_institution');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('invite');
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');

    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = $this->getFlowDataHandler()->getParameter('user');



    if ($par_data_person->save()) {
      // Add this person to the partnership and the appropriate authority or organisaiton.
      switch ($type) {
        case 'authority':
          $field = 'field_authority_person';
          if ($par_data_authority) {
            $par_data_authority->get('field_person')->appendItem($par_data_person);
            $par_data_authority->save();
          }

          break;

        case 'organisation':
          $field = 'field_organisation_person';
          if ($par_data_organisation) {
            $par_data_organisation->get('field_person')->appendItem($par_data_person);
            $par_data_organisation->save();
          }

          break;

      }
      if (isset($field)) {
        $par_data_partnership->get($field)->appendItem($par_data_person);
        $par_data_partnership->save();
      }

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
