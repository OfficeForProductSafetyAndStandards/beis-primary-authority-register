<?php

namespace Drupal\par_person_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_person_update_flows\ParFlowAccessTrait;
use Drupal\user\Entity\Role;
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
    // Set the user account that is being updated as a parameter for plugins to access
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = ParChooseAccount::getUserAccount($account_selection);

    if ($account) {
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('role', '', $cid_role_select);

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

    $cid_contact_details = $this->getFlowNegotiator()->getFormKey('par_person_update');
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
      $url = $this->getUrlGenerator()
        ->generateFromRoute($this->getFlowNegotiator()
          ->getFlow()
          ->progressRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    return parent::buildForm($form, $form_state);
  }

}
