<?php

namespace Drupal\par_person_create_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_person_create_flows\ParFlowAccessTrait;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * The form for choosing whether to create an account for this user.
 */
class ParAccountForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Give this person a user account?';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Select the user account that is being updated.
    $link_account_cid = $this->getFlowNegotiator()->getFormKey('user_account');
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $link_account_cid);
    $account = !empty($user_id) ? User::load($user_id) : NULL;
    if ($account) {
      $this->getFlowDataHandler()->setParameter('user', $account);
    }

    parent::loadData();
  }

}
