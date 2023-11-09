<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  public function loadData(int $index = 1): void {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions');

    $delta = $index - 1;

    // Get the cache IDs for the various forms that needs to be extracted from.
    $enforcement_actions_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_approve');

    // Check whether there are any referrable actions.
    foreach ($par_data_enforcement_actions as $delta => $action) {
      if (!$action->isReferrable()) {
        continue;
      }

      $status = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'primary_authority_status'], $enforcement_actions_cid);
      if ($status === ParDataEnforcementAction::REFERRED) {
        $this->setDefaultValuesByKey("notice_is_referrable", $index, TRUE);
      }
    }

    // Cardinality is not a zero-based index like the stored fields deltas.
    $par_data_enforcement_action = $par_data_enforcement_actions[$delta] ?? NULL;

    if ($par_data_enforcement_action && $par_data_enforcement_action->isReferrable()) {
      // Identify the authorities this notice can be referred to.
      $this->setDefaultValuesByKey("referable_options", $index, $par_data_enforcement_notice->getReferrableAuthorities());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // If there are no referred actions then skip this form.
    if (!$this->getDefaultValuesByKey('notice_is_referrable', $index, FALSE)) {
      $url = $this->getFlowNegotiator()->getFlow()->progress();
      return new RedirectResponse($url->toString());
    }

    if ($summary = $this->getDefaultValuesByKey('summary', $index, NULL)) {
      $form['referred_to_action'] = $this->renderMarkupField($summary);
    }

    if ($options = $this->getDefaultValuesByKey('referable_options', $index, NULL)) {
      $form['referred_to'] = [
        '#type' => 'radios',
        '#title' => $this->t('Choose an authority to refer to'),
        '#title_tag' => 'h2',
        '#options' => $options,
        '#default_value' => $this->getDefaultValuesByKey("referred_to", $index),
        '#required' => TRUE,
      ];
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions(array $actions = [], array $data = NULL): ?array {
    return $actions;
  }

}
