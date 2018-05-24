<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\par_data\Entity\ParDataEnforcementAction;
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

    // Check whether there are any referrable actions.
    foreach ($par_data_enforcement_actions as $action) {
      if (!$action->isReferrable()) {
        continue;
      }

      $this->setDefaultValuesByKey("notice_is_referrable", $cardinality, TRUE);
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
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
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
}
