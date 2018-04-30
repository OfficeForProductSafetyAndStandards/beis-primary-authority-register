<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "enforcement_action_review",
 *   title = @Translation("Form for reviewing enforcement actions.")
 * )
 */
class ParEnforcementActionReviewForm extends ParFormPluginBase {

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');
    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = isset($par_data_enforcement_actions[$cardinality-1]) ? $par_data_enforcement_actions[$cardinality-1] : NULL;

    if ($par_data_enforcement_action) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($par_data_enforcement_action->getEntityTypeId());

      $this->setDefaultValuesByKey("summary", $cardinality, $entity_view_builder->view($par_data_enforcement_action, 'full'));

      $this->setDefaultValuesByKey("is_referrable", $cardinality, $par_data_enforcement_action->isReferrable());
      $this->setDefaultValuesByKey("is_approvable", $cardinality, TRUE);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    if (!$this->getDefaultValuesByKey('is_approvable', $cardinality, FALSE)) {
      return $form;
    }

    $form['action'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['form-group', 'panel', 'panel-box']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    if ($summary = $this->getDefaultValuesByKey('summary', $cardinality, NULL)) {
      $form['action']['summary'] = $this->renderMarkupField($summary);
    }

    $statuses = [
      ParDataEnforcementAction::APPROVED => 'Allow',
      ParDataEnforcementAction::BLOCKED => 'Block',
    ];
    // Check whether this action can be referred.
    if ($this->getDefaultValuesByKey('is_referrable', $cardinality, FALSE)) {
      $statuses[ParDataEnforcementAction::REFERRED] = 'Refer';
    }

    $form['action']['primary_authority_status'] = [
      '#type' => 'radios',
      '#title' => $this->t('Decide to allow or block this action, or refer this action to another Primary Authority '),
      '#options' => $statuses,
      '#default_value' => $this->getDefaultValuesByKey(['action', 'primary_authority_status'], $cardinality, ParDataEnforcementAction::APPROVED),
      '#required' => TRUE,
    ];


    $form['action']['primary_authority_notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('If you plan to block this action you must provide the enforcing authority with a valid reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'primary_authority_notes'], $cardinality, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getElementName(['action', 'primary_authority_status'], $cardinality) . '"]' => ['value' => ParDataEnforcementAction::BLOCKED],
        ]
      ],
    ];
    $form['action']['referral_notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('If you plan to refer this action you must provide the enforcing authority with a valid reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'referral_notes'], $cardinality, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getElementName(['action', 'primary_authority_status'], $cardinality) . '"]' => ['value' => ParDataEnforcementAction::REFERRED],
        ]
      ],
    ];

    return $form;
  }
}
