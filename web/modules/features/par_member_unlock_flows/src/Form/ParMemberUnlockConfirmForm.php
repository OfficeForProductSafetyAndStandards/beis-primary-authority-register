<?php

namespace Drupal\par_member_unlock_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_member_unlock_flows\ParFlowAccessTrait;

/**
 * The confirmation screen for unlocking a membership list.
 */
class ParMemberUnlockConfirmForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Membership Unlock";

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Enter the revokcation reason.
    $form['unlock'] = [
      '#title' => $this->t('Would you like to unlock this membership list'),
      '#type' => 'fieldset',
    ];
    $form['unlock']['info'] = [
      '#type' => 'markup',
      '#markup' => "You should only be unlocking the membership list if there has been an error and a user has specifically requested it. In all other situations the list will be automatically unlocked after an hour.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $par_data_partnership->unlockMembership();
  }

}
