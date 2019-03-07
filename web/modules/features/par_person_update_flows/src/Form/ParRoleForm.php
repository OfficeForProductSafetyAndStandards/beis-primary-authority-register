<?php

namespace Drupal\par_person_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParCreateAccount;
use Drupal\par_person_update_flows\ParFlowAccessTrait;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for choosing which role to grant a user.
 */
class ParRoleForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Change the type of user';

  /**
   * Title callback default.
   */
  public function titleCallback() {
    if ($account = $this->getFlowDataHandler()->getParameter('user')) {
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
  public function loadData() {
    $cid_link_account = $this->getFlowNegotiator()
      ->getFormKey('user_account');
    $user_id = $this->getFlowDataHandler()
      ->getDefaultValues('user_id', NULL, $cid_link_account);

    $create_account_cid = $this->getFlowNegotiator()
      ->getFormKey('create_account');
    $create_account = $this->getFlowDataHandler()
      ->getDefaultValues('create_account', FALSE, $create_account_cid);

    // Skip the invitation process if a user id has already been matched
    // or the user has chosen not to add a user.
    if (!empty($user_id) || (!empty($create_account) && in_array($create_account, ParCreateAccount::IGNORE))) {
      $url = $this->getUrlGenerator()
        ->generateFromRoute($this->getFlowNegotiator()
          ->getFlow()
          ->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    parent::loadData();
  }

}
