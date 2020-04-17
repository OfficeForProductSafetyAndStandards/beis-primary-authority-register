<?php

namespace Drupal\par_partnership_contact_update_flows\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\invite\InviteConstants;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_partnership_contact_update_flows\ParFlowAccessTrait;
use Drupal\par_partnership_contact_update_flows\ParFormCancelTrait;
use Drupal\user\Entity\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The invitation form.
 */
class ParInviteForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Invite the person to create an account';

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
      $url = $this->getUrlGenerator()
        ->generateFromRoute($this->getFlowNegotiator()
          ->getFlow()
          ->progressRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    // Skip the invitation process if there is already a pending invitation for this user.
    // See if there are any outstanding invitations.
    $invitations = $this->getEntityTypeManager()
      ->getStorage('invite')
      ->loadByProperties([
        'field_invite_email_address' => $this->getFlowDataHandler()->getTempDataValue('to'),
        'status' => InviteConstants::INVITE_VALID
      ]);

    if (count($invitations) >= 1) {
      $invite = current($invitations);
      if ($invite->expires->value >= time()) {
        $url = $this->getUrlGenerator()
          ->generateFromRoute($this->getFlowNegotiator()
            ->getFlow()
            ->getNextRoute('next'), $this->getRouteParams());
        return new RedirectResponse($url);
      }
    }

    return parent::buildForm($form, $form_state);
  }

}
