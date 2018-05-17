<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
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
  public function loadData($cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // If an enforcement notice parameter is set use this.
    if ($par_data_enforcement_notice) {
      if ($par_data_enforcement_notice->hasField('notice_type')) {
        $this->getFlowDataHandler()->setFormPermValue("notice_type", $par_data_enforcement_notice->get('notice_type')->getString());
      }
      if ($par_data_enforcement_notice->hasField('summary')) {
        $this->getFlowDataHandler()->setFormPermValue("notice_summary", $par_data_enforcement_notice->summary->view('full'));
      }

      if ($par_data_organisation = $par_data_enforcement_notice->getEnforcedOrganisation(TRUE)) {
        $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
      }

      if ($par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
        $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      }
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $form['enforcement_notice'] = [
      '#type' => 'fieldset',
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Summary of notice'),
        '#attributes' => ['class' => 'heading-large'],
      ],
      'type' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => 'Type of notice: ' . $this->getDefaultValuesByKey('notice_type', $cardinality, NULL),
      ],
      'summary' => $this->getDefaultValuesByKey('notice_summary', $cardinality, NULL),
    ];

    // Add operation link for updating notice details.
    try {
      $form['enforcement_notice']['change_summary'] = [
        '#type' => 'markup',
        '#weight' => 99,
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('enforcement_details', $params, [])
            ->setText('Change the summary of this enforcement')
            ->toString(),
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }


    // Display the details for each Enforcement Action.
    if ($par_data_enforcement_actions = $this->getFlowDataHandler()->getParameter('par_data_enforcement_actions')) {
      $form['enforcement_actions'] = [
        '#type' => 'fieldset',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Enforcement Actions'),
          '#attributes' => ['class' => 'heading-large'],
        ]
      ];

      foreach ($par_data_enforcement_actions as $delta => $par_data_enforcement_action) {
        $form['enforcement_actions'][$delta] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => ['form-group', 'panel panel-border-wide']],
          'title' => [
            '#type' => 'html_tag',
            '#tag' => 'h3',
            '#value' => $par_data_enforcement_action->label(),
            '#attributes' => ['class' => 'heading-medium'],
          ],
          'status' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => 'Status: ' . $par_data_enforcement_action->getParStatus(),
          ],
          'regulatory_functions' => $par_data_enforcement_action->field_regulatory_function->view('full'),
          'details' => $par_data_enforcement_action->details->view('full'),
        ];

        // Add operation link for updating action details.
        try {
          $form['enforcement_actions'][$delta]['change_action'] = [
            '#type' => 'markup',
            '#weight' => 99,
            '#markup' => t('@link', [
              '@link' => $this->getFlowNegotiator()->getFlow()
                ->getLinkByCurrentOperation('enforcement_action', $params + ['cardinality' => $delta], [])
                ->setText('Change the details for ' . $par_data_enforcement_action->label())
                ->toString(),
            ]),
          ];
        }
        catch (ParFlowException $e) {

        }

        // Add operation link for updating action decision.
        try {
          $form['enforcement_actions'][$delta]['change_decision'] = [
            '#type' => 'markup',
            '#weight' => 99,
            '#markup' => t('@link', [
              '@link' => $this->getFlowNegotiator()->getFlow()
                ->getLinkByCurrentOperation('action_decision', $params, [])
                ->setText('Change response for ' . $par_data_enforcement_action->label())
                ->toString(),
            ]),
          ];
        }
        catch (ParFlowException $e) {

        }
      }
    }

    return $form;
  }
}
