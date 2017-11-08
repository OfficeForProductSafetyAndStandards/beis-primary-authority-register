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
class ParEnforcementSubmitNoticeForm extends ParBaseForm {

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
    $enforced_organisation = current($par_data_enforcement_notice->getEnforcedOrganisation());
    $enforced_legal_entity = current($par_data_enforcement_notice->getLegalEntity());
    $enforcing_officer = current($par_data_enforcement_notice->getEnforcingPerson());

    if ($enforced_legal_entity) {
      $enforced_legal_entity_name = $enforced_legal_entity->get('registered_name')->getString();
    } else {
      $enforced_legal_entity_name = $par_data_enforcement_notice->get('legal_entity_name')->getString();
    }

    // Load all enforcement actions for the current enforcement notification.
    $enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();

    $form['authority'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['authority']['authority_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Add an enforcement action'),
    ];

    $form['authority']['authority_name'] = [
      '#type' => 'markup',
      '#markup' => $enforced_authority->get('authority_name')->getString(),
      '#prefix' => '<div><h1>',
      '#suffix' => '</h1></div>',
    ];

    $form['organisation'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];


    $form['organisation']['organisation_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Regarding'),

    ];

    $form['organisation']['organisation_name'] = [
      '#type' => 'markup',
      '#markup' => $enforced_legal_entity_name,
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display the primary address.
    $form['registered_address'] = $this->renderSection('Registered address', $enforced_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);

    $form['enforcement_officer_name'] = $this->renderSection('Enforcing officer name', $enforcing_officer, ['first_name' => 'summary','last_name' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_officer_telephone'] = $this->renderSection('Enforcing officer telephone number', $enforcing_officer, ['work_phone' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_officer_email'] = $this->renderSection('Enforcing officer email address', $enforcing_officer, ['email' => 'summary'], [], TRUE, TRUE);

    $form['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'summary'], [], TRUE, TRUE);

    // Display all enforcement actions assigned to this enforcement action.
    foreach ($enforcement_actions as $enforcement_action) {
      $form[$enforcement_action->id()]['action_title'] = $this->renderSection('Proposed enforcement action', $enforcement_action, ['title' => 'title'], [], TRUE, TRUE);
      $form[$enforcement_action->id()]['action_regulatory_function'] = $this->renderSection('Regulatory function', $enforcement_action, ['field_regulatory_function' => 'summary'], [], TRUE, TRUE);
      $form[$enforcement_action->id()]['action_details'] = $this->renderSection('Details', $enforcement_action, ['details' => 'summary'], [], TRUE, TRUE);
      $form[$enforcement_action->id()]['action_attach'] = $this->renderMarkupField($enforcement_action->get('document')->view('full'));
    }

    $form['action_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Enforcement action(s)'),
      '#prefix' => '<h3><p>',
      '#suffix' => '</p></h3>',
    ];

    $form['action_add'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#description' => $this->t('If you are proposing more then one enforcement action, you should add these as separate actions using the link below'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $add_action_link = $this->getFlow()->getNextLink('add_enforcement_action')->setText('Add an enforcement action')->toString();
    $form['action_add']['add_link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $add_action_link]),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    $form['action_text'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action/s'),
      '#prefix' => '<h3><p>',
      '#suffix' => '</p></h3>',
    ];

    $form['action_notification'] = [
      '#type' => 'markup',
      '#markup' => $this->t('You will be notified by email of the outcome of this notification'),
      '#prefix' => '<h3><p>',
      '#suffix' => '</p></h3>',
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
