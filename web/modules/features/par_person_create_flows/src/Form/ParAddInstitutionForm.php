<?php

namespace Drupal\par_person_create_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_person_create_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for adding users to an institution.
 */
class ParAddInstitutionForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Title callback default.
   */
  protected $pageTitle = 'Choose their memberships';

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

    $this->getFlowDataHandler()->setFormPermValue('allow_multiple', TRUE);

    parent::loadData();
  }

}
