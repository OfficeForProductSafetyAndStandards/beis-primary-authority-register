<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_forms\ParFormBuilder;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "enforcement_action_review",
 *   title = @Translation("Form for reviewing enforcement actions.")
 * )
 */
class ParEnforcementActionReviewForm extends ParEnforcementActionDetail {

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');

    $delta = $index - 1;

    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = $par_data_enforcement_actions[$delta] ?? NULL;

    if ($par_data_enforcement_action) {
      $this->setDefaultValuesByKey("is_referrable", $index, $par_data_enforcement_action->isReferrable());
      $this->setDefaultValuesByKey("is_approvable", $index, TRUE);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Inherit the base plugin.
    $form = parent::getElements($form, $index);

    if (!$this->getDefaultValuesByKey('is_approvable', $index, FALSE)) {
      return $form;
    }

    $statuses = [
      ParDataEnforcementAction::APPROVED => 'Allow',
      ParDataEnforcementAction::BLOCKED => 'Block',
    ];
    // Check whether this action can be referred.
    if ($this->getDefaultValuesByKey('is_referrable', $index, FALSE)) {
      $statuses[ParDataEnforcementAction::REFERRED] = 'Refer';
    }

    $form['primary_authority_status'] = [
      '#type' => 'radios',
      '#title' => $this->t('Decide to allow or block this action, or refer this action to another Primary Authority '),
      '#title_tag' => 'h2',
      '#options' => $statuses,
      '#default_value' => $this->getDefaultValuesByKey('primary_authority_status', $index, ParDataEnforcementAction::APPROVED),
      '#required' => TRUE,
      '#weight' => 10,
    ];

    $form['primary_authority_notes'] = [
      '#type' => 'textarea',
      '#weight' => 11,
      '#title' => $this->t('If you plan to block this action you must provide the enforcing authority with a valid reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'primary_authority_notes'], $index, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getTargetName($this->getElementKey('primary_authority_status', $index)) . '"]' => ['value' => ParDataEnforcementAction::BLOCKED],
        ],
      ],
    ];
    $form['referral_notes'] = [
      '#type' => 'textarea',
      '#weight' => 11,
      '#title' => $this->t('If you plan to refer this action you must provide the enforcing authority with a valid reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'referral_notes'], $index, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getTargetName($this->getElementKey('primary_authority_status', $index)) . '"]' => ['value' => ParDataEnforcementAction::REFERRED],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $status_element = $this->getElement($form, ['primary_authority_status'], $index);
    $status = $status_element ? $form_state->getValue($status_element['#parents']) : NULL;

    // Set an error if an action is not reviewed.
    $allowed_statuses = [ParDataEnforcementAction::APPROVED, ParDataEnforcementAction::BLOCKED, ParDataEnforcementAction::REFERRED];
    if (empty($status) || !in_array($status, $allowed_statuses)) {
      $message = 'Please choose how you would like to respond to this notice.';
      $this->setError($form, $form_state, $status_element, $message);
    }

    $blocked_reason_element = $this->getElement($form, ['primary_authority_notes'], $index);
    $blocked_reason = $blocked_reason_element ? $form_state->getValue($blocked_reason_element['#parents']) : NULL;
    if ($status == ParDataEnforcementAction::BLOCKED && empty($blocked_reason)) {
      $message = 'You must explain your reason for blocking this notice.';
      $this->setError($form, $form_state, $blocked_reason_element, $message);
    }

    $referred_reason_element = $this->getElement($form, ['referral_notes'], $index);
    $referred_reason = $referred_reason_element ? $form_state->getValue($referred_reason_element['#parents']) : NULL;
    if ($status == ParDataEnforcementAction::REFERRED && empty($referred_reason)) {
      $message = 'You must explain why you are referring this notice.';
      $this->setError($form, $form_state, $referred_reason_element, $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
