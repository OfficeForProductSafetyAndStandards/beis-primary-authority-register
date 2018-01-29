<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataAuthority;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementSubmitNoticeForm extends ParBaseEnforcementForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_raise_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if (!$par_data_enforcement_notice) {
      return parent::titleCallback();
    }

    if ($par_data_enforcement_notice->get('legal_entity_name')->getString()) {
      $enforced_legal_entity_name = $par_data_enforcement_notice->get('legal_entity_name')->getString();
    }
    else {
      $enforced_legal_entity = current($par_data_enforcement_notice->getLegalEntity());
      $enforced_legal_entity_name = $enforced_legal_entity->get('registered_name')->getString();
    }
    $this->pageTitle = "Summary of the proposed enforcement action(s) regarding | {$enforced_legal_entity_name}";

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    $this->retrieveEditableValues();

    // Get the correct par_data_authority_id set by the previous form.
    $enforced_authority = current($par_data_enforcement_notice->getEnforcingAuthority());
    $enforcing_officer = current($par_data_enforcement_notice->getEnforcingPerson());

    // Load all enforcement actions for the current enforcement notification.
    $enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();

    $form['authority'] = $this->renderSection('Enforced by', $enforced_authority, ['authority_name' => 'summary']);

    $form['enforcement_officer_name'] = $this->renderSection('Enforcing officer name', $enforcing_officer, ['first_name' => 'summary','last_name' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_officer_telephone'] = $this->renderSection('Enforcing officer telephone number', $enforcing_officer, ['work_phone' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_officer_email'] = $this->renderSection('Enforcing officer email address', $enforcing_officer, ['email' => 'summary'], [], TRUE, TRUE);

    $form['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'full'], [], TRUE, TRUE);

    // Display all enforcement actions assigned to this enforcement action.
    foreach ($enforcement_actions as $enforcement_action) {
      $form[$enforcement_action->id()]['action_title'] = $this->renderSection('Proposed enforcement action', $enforcement_action, ['title' => 'title'], [], TRUE, TRUE);
      $form[$enforcement_action->id()]['action_regulatory_function'] = $this->renderSection('Regulatory function', $enforcement_action, ['field_regulatory_function' => 'summary'], [], TRUE, TRUE);
      $form[$enforcement_action->id()]['action_details'] = $this->renderSection('Details', $enforcement_action, ['details' => 'summary'], [], TRUE, TRUE);
      $form[$enforcement_action->id()]['action_attach'] = $this->renderSection('Attachments', $enforcement_action, ['document' => 'par_attachments'], [], TRUE);
    }

    $form['action_add'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => $this->t('Multiple actions'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $add_action_link = $this->getFlowNegotiator()->getFlow()->getNextLink('add_enforcement_action')->setText('Add another enforcement action')->toString();
    $form['action_add']['add_link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $add_action_link]),
    ];

    $form['action_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action/s'),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    $form['action_notification'] = [
      '#type' => 'markup',
      '#markup' => $this->t('You will be notified by email of the outcome of this notification'),
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
  }

}
