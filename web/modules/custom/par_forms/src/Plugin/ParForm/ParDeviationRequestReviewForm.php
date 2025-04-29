<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
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
  protected array $entityMapping = [
    ['notes', 'par_data_deviation_request', 'notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the details of this enquiry.'
    ]],
    ['files', 'par_data_deviation_request', 'document', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must submit a proposed inspection plan for this enquiry.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Inherit the base plugin.
    $form = parent::getElements($form, $index);

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
      '#title_tag' => 'h2',
      '#options' => $statuses,
      '#default_value' => $this->getDefaultValuesByKey('primary_authority_status', $index, ParDataDeviationRequest::APPROVED),
      '#required' => TRUE,
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    $form['primary_authority_notes'] = [
      '#type' => 'textarea',
      '#weight' => 11,
      '#title' => $this->t('If you plan to block this deviation request you must provide the enforcing authority with a reason.'),
      '#default_value' => $this->getDefaultValuesByKey(['action', 'primary_authority_notes'], $index, ''),
      '#states' => [
        'visible' => [
          ':input[name="' . $this->getTargetName($this->getElementKey('primary_authority_status', $index)) . '"]' => ['value' => ParDataDeviationRequest::BLOCKED],
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
    catch (ParFlowException) {

    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $status_element = $this->getElement($form, ['primary_authority_status'], $index);
    $status = $status_element ? $form_state->getValue($status_element['#parents']) : NULL;

    // Set an error if an action is not reviewed.
    $allowed_statuses = [ParDataDeviationRequest::APPROVED, ParDataDeviationRequest::BLOCKED];
    if (empty($status) || !in_array($status, $allowed_statuses)) {
      $message = 'Please choose how you would like to respond to this deviation request.';
      $this->setError($form, $form_state, $status_element, $message);
    }

    $blocked_reason_element = $this->getElement($form, ['primary_authority_notes'], $index);
    $blocked_reason = $blocked_reason_element ? $form_state->getValue($blocked_reason_element['#parents']) : NULL;
    if ($status == ParDataEnforcementAction::BLOCKED && empty($blocked_reason)) {
      $message = 'You must explain your reason for blocking this deviation request.';
      $this->setError($form, $form_state, $blocked_reason_element, $message);
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
