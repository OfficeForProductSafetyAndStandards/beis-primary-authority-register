<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Enforcement summary form plugin.
 *
 * @ParForm(
 *   id = "enforcement_action_detail",
 *   title = @Translation("Shows all of the enforcement action details.")
 * )
 */
class ParEnforcementActionDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');

    $delta = $index - 1;

    // If an enforcement notice parameter is set use this.
    if ($par_data_enforcement_notice && !$par_data_enforcement_actions) {
      $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();
    }

    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = $par_data_enforcement_actions[$delta] ?? NULL;

    if ($par_data_enforcement_action instanceof ParDataEnforcementAction) {
      $this->setDefaultValuesByKey("action_title", $index, $par_data_enforcement_action->label());

      if ($par_data_enforcement_action->getRawStatus() === ParDataEnforcementAction::APPROVED) {
        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), $par_data_enforcement_action->getParStatus());
      }
      elseif ($par_data_enforcement_action->getRawStatus() === ParDataEnforcementAction::BLOCKED) {
        $this->setDefaultValuesByKey("action_status_notes", $index, $par_data_enforcement_action->getPlain('primary_authority_notes'));

        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), $par_data_enforcement_action->getParStatus());
      }
      elseif ($par_data_enforcement_action->getRawStatus() === ParDataEnforcementAction::REFERRED) {
        $this->setDefaultValuesByKey("action_status_notes", $index, $par_data_enforcement_action->getPlain('referral_notes'));

        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), $par_data_enforcement_action->getParStatus());
      }
      else {
        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), 'created');
      }

      // Set the status description, if there is no revision data this should be the plain status.
      if ($description) {
        $this->setDefaultValuesByKey("action_status", $index, $description);
      }
      else {
        $this->setDefaultValuesByKey("action_status", $index, $par_data_enforcement_action->getParStatus());
      }

      if (!$par_data_enforcement_action->get('field_regulatory_function')->isEmpty()) {
        $this->setDefaultValuesByKey("action_regulatory_functions", $index, $par_data_enforcement_action->field_regulatory_function->view('full'));
      }
      $this->setDefaultValuesByKey("action_details", $index, $par_data_enforcement_action->details->view('full'));
      $this->setDefaultValuesByKey("action_attachments", $index, $par_data_enforcement_action->document->view('full'));
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    // Display the details for each Enforcement Action.
    if ($par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions')) {
      $form = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group', 'panel panel-border-wide']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#weight' => -2,
          '#value' => $this->getDefaultValuesByKey('action_title', $index),
          '#attributes' => ['class' => 'govuk-heading-m'],
        ],
        'status' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#weight' => -1,
          '#value' => $this->getDefaultValuesByKey('action_status', $index),
        ],
        'regulatory_functions' => $this->getDefaultValuesByKey('action_regulatory_functions', $index, []),
        'details' => $this->getDefaultValuesByKey('action_details', $index, []),
        'attachments' => $this->getDefaultValuesByKey('action_attachments', $index, []) + [
          '#attributes' => ['class' => ['govuk-form-group']],
        ],
      ];

      if ($notes = $this->getDefaultValuesByKey('action_status_notes', $index)) {
        $form['status_description'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#weight' => -1,
          '#value' => $notes,
        ];
      }

      // Add operation link for updating action details.
      try {
        $title = 'Change the details for ' . $this->getDefaultValuesByKey('action_title', $index);
        $link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('enforcement_action', $title, $params);
        $form['change_action'] = [
          '#type' => 'markup',
          '#weight' => 99,
          '#markup' => t('@link', [
            '@link' => $link ? $link->toString() : '',
          ]),
        ];
      }
      catch (ParFlowException $e) {

      }

      // Add operation link for updating action decision.
      try {
        $title = 'Change response for ' . $this->getDefaultValuesByKey('action_title', $index);
        $link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('action_decision', $title, $params);
        $form['change_decision'] = [
          '#type' => 'markup',
          '#weight' => 99,
          '#markup' => t('@link', [
            '@link' => $link ? $link->toString() : '',
          ]),
        ];
      }
      catch (ParFlowException) {

      }
    }

    return $form;
  }

  /**
   * Get the container wrapper for this component.
   */
  #[\Override]
  public function getWrapper() {
    $fieldset = parent::getWrapper();

    $fieldset['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Enforcement Actions'),
      '#attributes' => ['class' => 'govuk-heading-l'],
    ];

    return $fieldset;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  #[\Override]
  public function getComponentActions(array $actions = [], ?array $data = NULL): ?array {
    return $actions;
  }

}
