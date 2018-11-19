<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
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
  public function loadData($cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');

    // If an enforcement notice parameter is set use this.
    if ($par_data_enforcement_notice && !$par_data_enforcement_actions) {
      $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();
    }

    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = isset($par_data_enforcement_actions[$cardinality-1]) ? $par_data_enforcement_actions[$cardinality-1] : NULL;

    if ($par_data_enforcement_action && $par_data_enforcement_action instanceof ParDataEnforcementAction) {
      $this->setDefaultValuesByKey("action_title", $cardinality, $par_data_enforcement_action->label());

      if ($par_data_enforcement_action->getRawStatus() === ParDataEnforcementAction::APPROVED) {
        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), $par_data_enforcement_action->getParStatus());
      }
      elseif ($par_data_enforcement_action->getRawStatus() === ParDataEnforcementAction::BLOCKED) {
        $this->setDefaultValuesByKey("action_status_notes", $cardinality, $par_data_enforcement_action->getPrimaryAuthorityNotes());

        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), $par_data_enforcement_action->getParStatus());
      }
      elseif ($par_data_enforcement_action->getRawStatus() === ParDataEnforcementAction::REFERRED) {
        $this->setDefaultValuesByKey("action_status_notes", $cardinality, $par_data_enforcement_action->getReferralNotes());

        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), $par_data_enforcement_action->getParStatus());
      }
      else {
        $description = $par_data_enforcement_action->getStatusDescription($par_data_enforcement_action->getRawStatus(), 'created');
      }

      // Set the status description, if there is no revision data this should be the plain status.
      if ($description) {
        $this->setDefaultValuesByKey("action_status", $cardinality, $description);
      }
      else {
        $this->setDefaultValuesByKey("action_status", $cardinality, $par_data_enforcement_action->getParStatus());
      }

      if (!$par_data_enforcement_action->get('field_regulatory_function')->isEmpty()) {
        $this->setDefaultValuesByKey("action_regulatory_functions", $cardinality, $par_data_enforcement_action->field_regulatory_function->view('full'));
      }
      $this->setDefaultValuesByKey("action_details", $cardinality, $par_data_enforcement_action->details->view('full'));
      $this->setDefaultValuesByKey("action_attachments", $cardinality, $par_data_enforcement_action->document->view('full'));
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    // Display the details for each Enforcement Action.
    if ($par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions')) {
      $form = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group', 'panel panel-border-wide']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#weight' => -2,
          '#value' => $this->getDefaultValuesByKey('action_title', $cardinality),
          '#attributes' => ['class' => 'heading-medium'],
        ],
        'status' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#weight' => -1,
          '#value' => $this->getDefaultValuesByKey('action_status', $cardinality),
        ],
        'regulatory_functions' => $this->getDefaultValuesByKey('action_regulatory_functions', $cardinality, []),
        'details' => $this->getDefaultValuesByKey('action_details', $cardinality, []),
        'attachments' => $this->getDefaultValuesByKey('action_attachments', $cardinality, []) + [
          '#attributes' => ['class' => ['form-group']]
        ],
      ];

      if ($notes = $this->getDefaultValuesByKey('action_status_notes', $cardinality)) {
        $form['status_description'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#weight' => -1,
          '#value' => $notes,
        ];
      }

      // Add operation link for updating action details.
      try {
        $form['change_action'] = [
          '#type' => 'markup',
          '#weight' => 99,
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()
              ->getLinkByCurrentOperation('enforcement_action', $params, [])
              ->setText('Change the details for ' . $this->getDefaultValuesByKey('action_title', $cardinality))
              ->toString(),
          ]),
        ];
      }
      catch (ParFlowException $e) {

      }

      // Add operation link for updating action decision.
      try {
        $form['change_decision'] = [
          '#type' => 'markup',
          '#weight' => 99,
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()
              ->getLinkByCurrentOperation('action_decision', $params, [])
              ->setText('Change response for ' . $this->getDefaultValuesByKey('action_title', $cardinality))
              ->toString(),
          ]),
        ];
      }
      catch (ParFlowException $e) {

      }
    }

    return $form;
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getWrapper() {
    $fieldset = parent::getWrapper();

    $fieldset['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Enforcement Actions'),
      '#attributes' => ['class' => 'heading-large'],
    ];

    return $fieldset;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
  
}
