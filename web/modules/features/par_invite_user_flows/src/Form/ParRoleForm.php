<?php

namespace Drupal\par_invite_user_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_invite_user_flows\ParFlowAccessTrait;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * The form for choosing which role to grant a user.
 */
class ParRoleForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Change the type of user';

  /**
   * Title callback default.
   */
  #[\Override]
  public function titleCallback() {
    // If there is an existing user selected force a user role to be choosen.
    $link_account_cid = $this->getFlowNegotiator()->getFormKey('par_profile_update_link');
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $link_account_cid);
    $linked_account = !empty($user_id) ? User::load($user_id) : NULL;

    if ($linked_account) {
      $this->pageTitle = 'Change the type of user';
    }
    else {
      $this->pageTitle = 'What type of user would you like to create?';
    }

    return parent::titleCallback();
  }


  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData() {
    parent::loadData();

    // Remove the empty value, a user account must be invited in this journey.
    $role_options = (array) $this->getFlowDataHandler()->getFormPermValue("roles_options");
    $role_options = array_filter($role_options, fn($k) => !empty($k), ARRAY_FILTER_USE_KEY);

    $this->getFlowDataHandler()->setFormPermValue("roles_options", $role_options);
  }

}
