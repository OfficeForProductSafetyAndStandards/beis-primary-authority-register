<?php

namespace Drupal\par_enforcement_review_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementApproveNoticeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Make a decision | Proposed enforcement action(s)";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    if ($par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice')) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_notice->getEnforcementActions());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

//    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
//    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
//      $form_data = $form_state->getValue(['actions', $delta], 'par_enforcement_notice_approve');
//
//      // Set an error if an action is not reviewed.
//      if (!isset($form_data['primary_authority_status']) || empty($form_data['primary_authority_status'])) {
//        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'Every action in this notice must be reviewed before you can proceed.');
//      }
//
//      if ($form_data['primary_authority_status'] == ParDataEnforcementAction::BLOCKED && empty($form_data['primary_authority_notes'])) {
//        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'If you plan to block this action you must provide the enforcing authority with a valid reason.');
//      }
//
//      if ($form_data['primary_authority_status'] == ParDataEnforcementAction::REFERRED && empty($form_data['referral_notes'])) {
//        $this->setElementError(['actions', $delta, 'referral_notes'], $form_state, 'If you plan to refer this action you must provide the enforcing authority with a valid reason.');
//      }
//
//      // Set an error if this action has already been reviewed.
//      if ($action->isApproved() || $action->isBlocked() || $action->isReferred()) {
//        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'This action has already been reviewed.');
//      }
//
//      // Set an error if it is not possible to change to this status.
//      if (!isset($form_data['primary_authority_status']) || !$action->canTransition($form_data['primary_authority_status'])) {
//        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'This action cannot be changed because it has already been reviewed.');
//      }
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }
}
