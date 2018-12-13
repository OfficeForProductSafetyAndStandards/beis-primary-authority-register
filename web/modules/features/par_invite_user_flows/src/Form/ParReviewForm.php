<?php

namespace Drupal\par_invite_user_flows\Form;

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
use Drupal\par_invite_user_flows\ParFlowAccessTrait;
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
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPerson $par_data_person */
    /** @var User $account */
    /** @var ParDataAuthority[] $par_data_authority */
    /** @var ParDataOrganisation[] $par_data_organisation */

    // Get the cache IDs for the various forms that needs needs to be extracted from.
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
      $role = Role::load($rid);
      $role_options = $this->getParDataManager()->getEntitiesAsOptions([$role], []);
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
      default:

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
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
        '#attributes' => ['class' => 'form-group'],
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

    $message = $this->getFlowDataHandler()->getDefaultValues('email_body', '');
    $subject = $this->getFlowDataHandler()->getDefaultValues('email_subject', '');
    if (!empty($subject) || !empty($message)) {
      $form['message'] = [
        '#type' => 'fieldset',
        '#title' => 'Message',
        '#attributes' => ['class' => 'form-group'],
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

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $link_account_cid = $this->getFlowNegotiator()->getFormKey('par_profile_update_link');
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('par_invite_institution');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('par_invite_institution');

    // If there is an existing user attach it to this person.
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $link_account_cid);
    $account = $user_id ? User::load($user_id) : $this->getFlowDataHandler()->getParameter('user');
    if ($account) {
      $par_data_person->setUserAccount($account);
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
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('par_invite_institution');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('par_invite_institution');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('par_invite');

    // Override invite type if there were multiple roles to choose from.
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

    // Regardless of whether an invitation is being set,
    // update the authorities and organisations with the person.
    $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_authority_cid);
    if ($authority_ids && (in_array($role, ['par_authority', 'par_enforcement']) || !$role)) {
      $par_data_person->updateAuthorityMemberships($authority_ids, TRUE);
    }
    $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_organisation_cid);
    if ($organisation_ids && ($role === 'par_organisation' || !$role)) {
      $par_data_person->updateOrganisationMemberships($organisation_ids, TRUE);
    }

    if (!isset($invitation_type) || (isset($invite) && $invite->save())) {
      // Save the user to invalidate the cache.
      $par_data_person->save();

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
