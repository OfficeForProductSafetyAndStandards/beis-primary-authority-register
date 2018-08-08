<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

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
  public function loadData($cardinality = 1) {
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');
    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = isset($par_data_enforcement_actions[$cardinality-1]) ? $par_data_enforcement_actions[$cardinality-1] : NULL;

    if ($par_data_enforcement_action) {
      $this->setDefaultValuesByKey("is_referrable", $cardinality, $par_data_enforcement_action->isReferrable());
      $this->setDefaultValuesByKey("is_approvable", $cardinality, TRUE);
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Inherit the base plugin.
    $form = parent::getElements($form, $cardinality);

    if (!$this->getDefaultValuesByKey('is_approvable', $cardinality, FALSE)) {
      return $form;
    }

    $statuses = [
      ParDataEnforcementAction::APPROVED => 'Allow',
      ParDataEnforcementAction::BLOCKED => 'Block',
    ];
    // Check whether this action can be referred.
    if ($this->getDefaultValuesByKey('is_referrable', $cardinality, FALSE)) {
      $statuses[ParDataEnforcementAction::REFERRED] = 'Refer';
    }

    $form['primary_authority_status'] = [
      '#type' => 'radios',
      '#weight' => 10,
      '#title' => $this->t('Decide to allow or block this action, or refer this action to another Primary Authority '),
      '#options' => $statuses,
      '#default_value' => $this->getDefaultValuesByKey('primary_authority_status', $cardinality, ParDataEnforcementAction::APPROVED),
      '#required' => TRUE,
    ];


    $form['primary_authority_notes'] = [
      '#type' => 'textarea',
      '#weight' => 11,
      '#title' => $this->t('If you plan to block this action you must provide the enforcing authority with a valid reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'primary_authority_notes'], $cardinality, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getElementName('primary_authority_status', $cardinality) . '"]' => ['value' => ParDataEnforcementAction::BLOCKED],
        ]
      ],
    ];
    $form['referral_notes'] = [
      '#type' => 'textarea',
      '#weight' => 11,
      '#title' => $this->t('If you plan to refer this action you must provide the enforcing authority with a valid reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'referral_notes'], $cardinality, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getElementName('primary_authority_status', $cardinality) . '"]' => ['value' => ParDataEnforcementAction::REFERRED],
        ]
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
//    $legal_entity = $this->getElementKey('legal_entities_select');
//    $alternative_legal_entity = $this->getElementKey('alternative_legal_entity');
//    if (empty($form_state->getValue($legal_entity)) && empty($form_state->getValue($alternative_legal_entity))) {
//      $form_state->setErrorByName($legal_entity, $this->t('<a href="#edit-legal_entities_select">You must choose a legal entity.</a>'));
//    }

    $status_key = $this->getElementKey(['action', 'primary_authority_status']);
    $status = $this->getFlowDataHandler()->getTempDataValue($status_key);

    // Set an error if an action is not reviewed.
    $allowed_statuses = [ParDataEnforcementAction::APPROVED, ParDataEnforcementAction::BLOCKED, ParDataEnforcementAction::REFERRED];
    $definition = DataDefinition::create('string')
      ->addConstraint('AllowedValues', ['choices' => $allowed_statuses]);
    $typed_data = \Drupal::typedDataManager()->create($definition, $status);
    $violations['primary_authority_status'] = $typed_data->validate();

    $blocked_reason_key = $this->getElementKey(['action', 'primary_authority_notes']);
    $blocked_reason = $this->getFlowDataHandler()->getTempDataValue($blocked_reason_key);
    $definition = DataDefinition::create('string')
      ->addConstraint('NotNull');
    $typed_data = \Drupal::typedDataManager()->create($definition, $blocked_reason);
    if ($status == ParDataEnforcementAction::BLOCKED && empty($blocked_reason)) {
      $violations['primary_authority_notes'] = $typed_data->validate();
    }

    $referred_reason_key = $this->getElementKey(['action', 'referral_notes']);
    $referred_reason = $this->getFlowDataHandler()->getTempDataValue($referred_reason_key);
    $definition = DataDefinition::create('string')
      ->addConstraint('NotNull');
    $typed_data = \Drupal::typedDataManager()->create($definition, $referred_reason);
    if ($status == ParDataEnforcementAction::REFERRED && empty($referred_reason)) {
      $violations['referral_notes'] = $typed_data->validate();
    }

    // Set an error if this action has already been reviewed.
//    if ($action->isApproved() || $action->isBlocked() || $action->isReferred()) {
//      $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'This action has already been reviewed.');
//    }

    // Set an error if it is not possible to change to this status.
//    if (!isset($form_data['primary_authority_status']) || !$action->canTransition($form_data['primary_authority_status'])) {
//      $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'This action cannot be changed because it has already been reviewed.');
//    }

    return parent::validate($form, $form_state, $cardinality, $action);
  }
}
