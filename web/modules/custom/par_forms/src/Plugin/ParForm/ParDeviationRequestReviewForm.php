<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Deviation request review form plugin.
 *
 * @ParForm(
 *   id = "deviation_request_review",
 *   title = @Translation("The deviation request review form.")
 * )
 */
class ParDeviationRequestReviewForm extends ParDeviationRequestDetail {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Inherit the base plugin.
    $form = parent::getElements($form, $cardinality);

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $statuses = [
      ParDataDeviationRequest::APPROVED => 'Allow',
      ParDataDeviationRequest::BLOCKED => 'Block',
    ];

    $form['primary_authority_status'] = [
      '#type' => 'radios',
      '#weight' => 10,
      '#title' => $this->t('Decide to allow or block this request to deviate from an inspection plan'),
      '#options' => $statuses,
      '#default_value' => $this->getDefaultValuesByKey('primary_authority_status', $cardinality, ParDataDeviationRequest::APPROVED),
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-group']],
    ];

    $form['primary_authority_notes'] = [
      '#type' => 'textarea',
      '#weight' => 11,
      '#title' => $this->t('If you plan to block this deviation request you must provide the enforcing authority with a reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'primary_authority_notes'], $cardinality, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getTargetName($this->getElementKey('primary_authority_status', $cardinality)) . '"]' => ['value' => ParDataDeviationRequest::BLOCKED],
        ]
      ],
    ];

    // Add operation link for updating deviation review decision.
    try {
      $form['change_decision'] = [
        '#type' => 'markup',
        '#weight' => 99,
        '#markup' => t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()
            ->getLinkByCurrentOperation('deviation_decision', $params, [])
            ->setText('Change response for this reivew')
            ->toString(),
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }

    return $form;
  }
}
