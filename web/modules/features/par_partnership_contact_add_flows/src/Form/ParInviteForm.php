<?php

namespace Drupal\par_partnership_contact_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_partnership_contact_add_flows\ParFlowAccessTrait;
use Drupal\par_roles\ParRoleManager;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The invitation form.
 */
class ParInviteForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Invite the person to create an account';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person');

    // Get the cache IDs for the various forms that needs to be extracted from.
    $link_account_cid = $this->getFlowNegotiator()->getFormKey('link_account');
    $select_memberships_cid = $this->getFlowNegotiator()->getFormKey('select_memberships');
    $cid_role_select = $this->getFlowNegotiator()->getFormKey('select_role');

    // If there is an existing user attach it to this person.
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $link_account_cid);
    $account = !empty($user_id) ? User::load($user_id) : $this->getFlowDataHandler()->getParameter('user');
    if ($account) {
      $par_data_person->setUserAccount($account);
    }

    // Get the authorities and organisation memberships for the person.
    $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_memberships_cid);
    $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_memberships_cid);

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
    $institution_type = !empty($authority_ids) ? 'par_data_authority' :
      (!empty($organisation_ids) ? 'par_data_organisation' : NULL);
    foreach ($this->getParRoleManager()->getRolesByHierarchy(NULL, $institution_type) as $role) {
      if (in_array($role, $roles) && isset($invites_types[$role])) {
        $invitation_type = $invites_types[$role];
        break;
      }
    }

    $role_options = $this->getParDataManager()->getEntitiesAsOptions(Role::loadMultiple($roles));

    // The invitation type must be set first.
    $this->getFlowDataHandler()->setFormPermValue('invitation_type', $invitation_type ?? 'none');
    $this->getFlowDataHandler()->setFormPermValue("roles", $role_options);

    if ($par_data_person instanceof ParDataPersonInterface) {
      $this->getFlowDataHandler()->setTempDataValue('to', $par_data_person->getEmail());
      $this->getFlowDataHandler()->setFormPermValue('recipient_name', $par_data_person->getFirstName());
    }

    parent::loadData();


    // Set the user account that is being updated as a parameter for plugins to access
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = ParChooseAccount::getUserAccount($account_selection);

    if ($account) {
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('roles', '', $cid_role_select);

    switch ($role) {
      case 'par_enforcement':
        $invitation_type = 'invite_enforcement_officer';
        $role_options = $this->getParDataManager()->getEntitiesAsOptions([Role::load($role)], []);

        break;
      case 'par_authority':
        $invitation_type = 'invite_authority_member';
        $role_options = $this->getParDataManager()->getEntitiesAsOptions([Role::load($role)], []);

        break;
      case 'par_organisation':
        $invitation_type = 'invite_organisation_member';
        $role_options = $this->getParDataManager()->getEntitiesAsOptions([Role::load($role)], []);

        break;
      default:
        $invitation_type = 'none';
        $role_options = [];
    }

    // The invitation type must be set first.
    $this->getFlowDataHandler()->setFormPermValue('invitation_type', $invitation_type);
    $this->getFlowDataHandler()->setFormPermValue("roles", $role_options);

    $cid_contact_details = $this->getFlowNegotiator()->getFormKey('par_add_contact');
    if ($email = $this->getFlowDataHandler()->getDefaultValues('email', NULL, $cid_contact_details)) {
      $this->getFlowDataHandler()->setTempDataValue('to', $email);
    }
    if ($name = $this->getFlowDataHandler()->getDefaultValues('first_name', NULL, $cid_contact_details)) {
      $this->getFlowDataHandler()->setFormPermValue("recipient_name", $name);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);

    // Skip the invitation process if a user id has already been matched
    // or the user has chosen not to add a user.
    if ($account_selection !== ParChooseAccount::CREATE) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    // Change the action to save.
    $this->getFlowNegotiator()->getFlow()->setActions(['next', 'cancel']);
    $this->getFlowNegotiator()->getFlow()->setPrimaryActionTitle('Invite');

    return parent::buildForm($form, $form_state);
  }

}
