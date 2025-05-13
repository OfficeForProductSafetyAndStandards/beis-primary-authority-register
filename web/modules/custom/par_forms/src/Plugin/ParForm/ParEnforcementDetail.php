<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Enforcement summary form plugin.
 *
 * @ParForm(
 *   id = "enforcement_detail",
 *   title = @Translation("The full enforcement display.")
 * )
 */
class ParEnforcementDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // If an enforcement notice parameter is set use this.
    if ($par_data_enforcement_notice) {
      if ($par_data_enforcement_notice->hasField('notice_type')) {
        $this->getFlowDataHandler()->setFormPermValue("notice_type", $par_data_enforcement_notice->get('notice_type')->getString());
      }
      if ($par_data_enforcement_notice->hasField('summary')) {
        $this->getFlowDataHandler()->setFormPermValue("notice_summary", $par_data_enforcement_notice->summary->view('full'));
      }

      if ($par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
        $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      }
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

    $form['enforcement_notice'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Summary of notice'),
        '#attributes' => ['class' => 'govuk-heading-l'],
      ],
      'type' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => 'Type of notice: ' . $this->getDefaultValuesByKey('notice_type', $index, NULL),
      ],
      'summary' => $this->getDefaultValuesByKey('notice_summary', $index, NULL),
    ];

    // Add operation link for updating notice details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('enforcement_details', 'Change the summary of this enforcement', $params);
      $form['enforcement_notice']['change_summary'] = [
        '#type' => 'markup',
        '#weight' => 99,
        '#markup' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException) {

    }

    return $form;
  }

}
