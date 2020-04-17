<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "enforcement_action_refer",
 *   title = @Translation("Form for referring enforcement actions.")
 * )
 */
class ParEnforcementActionReferForm extends ParFormPluginBase {

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $enforcement_actions_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_approve');

    // Check whether there are any referrable actions.
    foreach ($par_data_enforcement_actions as $delta => $action) {
      if (!$action->isReferrable()) {
        continue;
      }

      $status = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'primary_authority_status'], $enforcement_actions_cid);
      if ($status === ParDataEnforcementAction::REFERRED) {
        $this->setDefaultValuesByKey("notice_is_referrable", $cardinality, TRUE);
      }
    }

    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = isset($par_data_enforcement_actions[$cardinality-1]) ? $par_data_enforcement_actions[$cardinality-1] : NULL;

    if ($par_data_enforcement_action && $par_data_enforcement_action->isReferrable()) {
      // Identify the authorities this notice can be referred to.
      $this->setDefaultValuesByKey("referable_options", $cardinality, $par_data_enforcement_notice->getReferrableAuthorities());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // If there are no referred actions then skip this form.
    if (!$this->getDefaultValuesByKey('notice_is_referrable', $cardinality, FALSE)) {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute('next'), $this->getRouteParams());
      return new RedirectResponse($url);
    }

    if ($summary = $this->getDefaultValuesByKey('summary', $cardinality, NULL)) {
      $form['referred_to_action'] = $this->renderMarkupField($summary);
    }

    if ($options = $this->getDefaultValuesByKey('referable_options', $cardinality, NULL)) {
      $form['referred_to'] = [
        '#type' => 'radios',
        '#title' => $this->t('Choose an authority to refer to'),
        '#options' => $options,
        '#default_value' => $this->getDefaultValuesByKey("referred_to", $cardinality),
        '#required' => TRUE,
      ];
    }

    return $form;
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
