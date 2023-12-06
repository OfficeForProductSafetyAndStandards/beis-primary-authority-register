<?php

namespace Drupal\par_invite_user_flows\Form;

use Drupal\Component\Utility\NestedArray;
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
use Drupal\par_invite_user_flows\ParFlowAccessTrait;
use Drupal\par_roles\ParRoleException;
use Drupal\par_roles\ParRoleManager;
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Invitation review';

  /**
   * Get the PAR Role manager.
   */
  protected function getParRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPerson $par_data_person */
    /** @var User $account */
    /** @var Invite $invite */
    /** @var [] $roles */
    /** @var ParDataAuthority[] $par_data_authority */
    /** @var ParDataOrganisation[] $par_data_organisation */

    // Get the cache IDs for the various forms that needs to be extracted from.
    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('par_invite');

    // Set the basic details for the contact.
    if ($par_data_person) {
      $this->getFlowDataHandler()->setFormPermValue("full_name", $par_data_person->getFullName());
      $this->getFlowDataHandler()->setFormPermValue("email", $par_data_person->getEmail());
    }

    // If there is an existing user display this.
    if ($account) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'existing');
    }
    else {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'invited');

      // Show the role they've being invited to perform.
      $rid = $this->getFlowDataHandler()->getDefaultValues('role', NULL, $cid_role_select);
      $role = !empty($rid) ? Role::load($rid) : NULL;
      $role_options = $role ? $this->getParDataManager()->getEntitiesAsOptions([$role], []) : NULL;
      $this->getFlowDataHandler()->setFormPermValue("role", isset($role_options[$rid]) ? $role_options[$rid]: '(none)');
    }

    if ($par_data_authority) {
      $authority_names = $this->getParDataManager()->getEntitiesAsOptions($par_data_authority, []);
      $this->getFlowDataHandler()->setFormPermValue("authorities", implode('<br>', $authority_names));
    }
    if ($par_data_organisation) {
      $organisation_names = $this->getParDataManager()->getEntitiesAsOptions($par_data_organisation, []);
      $this->getFlowDataHandler()->setFormPermValue("organisations", implode('<br>', $organisation_names));
    }

    $subject = $this->getFlowDataHandler()->getDefaultValues('subject', NULL, $cid_invitation);
    $message = $this->getFlowDataHandler()->getDefaultValues('body', NULL, $cid_invitation);
    if ($subject) {
      $this->getFlowDataHandler()->setFormPermValue("email_subject", $subject);
    }
    if ($message) {
      $this->getFlowDataHandler()->setFormPermValue("email_body", $message);
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
        '#attributes' => ['class' => 'govuk-form-group'],
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
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('email', ''),
        ]
      ],
    ];

    switch ($this->getFlowDataHandler()->getDefaultValues("user_status", NULL)) {
      case 'existing':

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'govuk-form-group'],
          '#title' => 'User account',
          [
            '#markup' => "A user account already exists for this person.",
          ],
        ];

        break;

      case 'invited':
      default:

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'govuk-form-group'],
          '#title' => 'User account',
          [
            '#markup' => "An invitation will be sent to this person to invite them to join the Primary Authority Register.",
          ],
        ];

        // Also change the primary action text.
        $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Send invitation');

        break;
    }

    if ($role = $this->getFlowDataHandler()->getFormPermValue("role")) {
      $form['target_role'] = [
        '#type' => 'fieldset',
        '#title' => 'Type of user',
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => $role,
        ]
      ];
    }

    $form['memberships'] = [
      '#type' => 'fieldset',
    ];
    if ($authorities = $this->getFlowDataHandler()->getDefaultValues('authorities', NULL)) {
      $form['memberships']['authorities'] = [
        '#type' => 'fieldset',
        '#title' => 'Belongs to the following authorities',
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => $authorities,
        ]
      ];
    }
    if ($organisations = $this->getFlowDataHandler()->getDefaultValues('organisations', NULL)) {
      $form['memberships']['organisations'] = [
        '#type' => 'fieldset',
        '#title' => 'Belongs to the following organisations',
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => $organisations,
        ]
      ];
    }

    $message = $this->getFlowDataHandler()->getDefaultValues('email_body', '');
    $subject = $this->getFlowDataHandler()->getDefaultValues('email_subject', '');
    if (!empty($subject) || !empty($message)) {
      $form['message'] = [
        '#type' => 'fieldset',
        '#title' => 'Message',
        '#attributes' => ['class' => 'govuk-form-group'],
        [
          '#markup' => '<p><i>' . $subject . '</i></p>',
        ],
        [
          '#markup' => '<p>' . nl2br($message) . '</p>',
        ]
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    // Get the cache IDs for the various forms that needs to be extracted from.
    $link_account_cid = $this->getFlowNegotiator()->getFormKey('link_account');
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('select_memberships');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('select_memberships');
    $cid_role_select = $this->getFlowNegotiator()->getFormKey('select_role');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('par_invite');

    // If there is an existing user attach it to this person.
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $link_account_cid);
    $account = !empty($user_id) ? User::load($user_id) : $this->getFlowDataHandler()->getParameter('user');
    if ($account) {
      $par_data_person->setUserAccount($account);
    }

    // Get the authorities and organisation memberships for the person.
    $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_authority_cid);
    $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_organisation_cid);
    $par_data_authorities = $par_data_person->updateAuthorityMemberships($authority_ids);
    $par_data_organisations = $par_data_person->updateOrganisationMemberships($organisation_ids);

    // Get the general roles.
    $roles = array_filter((array) $this->getFlowDataHandler()->getDefaultValues('general', [], $cid_role_select));
    // Get the institutional roles.
    foreach (ParRoleManager::INSTITUTION_ROLES as $institution_type => $institution_roles) {
      $institution_roles = array_filter((array) $this->getFlowDataHandler()->getDefaultValues($institution_type, [], $cid_role_select));
      $roles += $institution_roles;
    }

    $invites_types = [
      'national_regulator' => 'invite_national_regulator',
      'par_enforcement' => 'invite_enforcement_officer',
      'par_authority' => 'invite_authority_member',
      'par_authority_manager' => 'invite_authority_manager',
      'par_organisation' => 'invite_organisation_member',
      'par_organisation_manager' => 'invite_organisation_manager',
      'par_helpdesk' => 'invite_processing_team_member',
      'senior_administration_officer' => 'invite_senior_administration_officer',
    ];
    // Because we can only send out one invite even if the user has multiple roles.
    // First try to send the invites for the general roles, because the others
    // will be automatically assigned based on memberships.
    $institution_type = !empty($par_data_authorities) ? 'par_data_authority' :
      (!empty($par_data_organisations) ? 'par_data_organisation' : NULL);
    foreach ($this->getParRoleManager()->getRolesByHierarchy($institution_type) as $role) {
      if (in_array($role, $roles) && isset($invites_types[$role])) {
        $invitation_type = $invites_types[$role];
        break;
      }
    }

    // Create invitation if an invitation type has been set and no existing user has been found.
    if (isset($invitation_type) && !$account) {
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

    return [
      'par_data_person' => $par_data_person,
      'account' => $account,
      'invite' => $invite,
      'roles' => $roles,
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
    /** @var Invite $invite */
    /** @var [] $roles */
    /** @var ParDataAuthority[] $par_data_authority */
    /** @var ParDataOrganisation[] $par_data_organisation */

    // Save the person.
    $par_data_person->save();

    // Update the institutional memberships after the person is saved.
    $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_authority_cid);
    if (!empty($authority_ids)) {
      $par_data_person->updateAuthorityMemberships($authority_ids, TRUE);
    }
    $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_organisation_cid);
    if (!empty($organisation_ids)) {
      $par_data_person->updateOrganisationMemberships($organisation_ids, TRUE);
    }

    // Roles MUST be added after the memberships are saved.
    foreach ($roles as $role) {
      try {
        $account = $this->getParRoleManager()->addRole($account, $role);
      }
      catch (ParRoleException $ignore) {

      }
    }

    if ($invite instanceof InviteInterface && $invite->save()) {
      // Save the user to invalidate the cache.
      $account?->save();

      // Also invalidate the user account cache if there is one.
      if ($account) {
        \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);
      }

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('This invite could not be sent for %person on %form_id');
      $replacements = [
        '%invite' => $this->getFlowDataHandler()->getTempDataValue('first_name') . ' ' . $this->getFlowDataHandler()->getTempDataValue('last_name'),
        '%person' => $this->getFlowDataHandler()->getTempDataValue('recipient_email'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
