<?php

namespace Drupal\par_person_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
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
    // Set the user account that is being updated as a parameter for plugins to access
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = ParChooseAccount::getUserAccount($account_selection);

    if ($account) {
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    // Determine whether the person has any organisation or authority memberships
    // and can be given organisation roles.
    $select_institution_cid = $this->getFlowNegotiator()->getFormKey('par_update_institution');
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_institution_cid);
      $organisations = $par_data_person->updateOrganisationMemberships($organisation_ids, FALSE);

      $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_institution_cid);
      $authorities = $par_data_person->updateAuthorityMemberships($authority_ids, FALSE);

      // If there aren't any organisations for this user then organisation roles can't be assinged.
      if (empty($organisations)) {
        $this->getFlowDataHandler()->setFormPermValue("user_has_organisation", FALSE);
      }

      // If there aren't any authorities for this user then authority roles can't be assinged.
      if (empty($authorities)) {
        $this->getFlowDataHandler()->setFormPermValue("user_has_authority", FALSE);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Skip the invitation process if a user id has already been matched
    // or the user has chosen not to add a user.
    $choose_account_cid = $this->getFlowNegotiator()->getFormKey('choose_account');
    $account_selection = $this->getFlowDataHandler()->getDefaultValues('account', NULL, $choose_account_cid);
    $account = $this->getFlowDataHandler()->getParameter('user');

    if (!$account && $account_selection !== ParChooseAccount::CREATE) {
      $url = $this->getUrlGenerator()
        ->generateFromRoute($this->getFlowNegotiator()
          ->getFlow()
          ->getNextRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    return parent::buildForm($form, $form_state);
  }

}
