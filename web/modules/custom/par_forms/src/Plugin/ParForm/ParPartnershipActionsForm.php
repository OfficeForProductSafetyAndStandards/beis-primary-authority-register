<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Partnership actions form plugin.
 *
 * @ParForm(
 *   id = "partnership_actions",
 *   title = @Translation("Partnership actions form.")
 * )
 */
class ParPartnershipActionsForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    // Create a list of links for the actions that can be performed on this partnership.
    $form['partnership_actions_title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => t('Send a message about this organisation'),
      '#attributes' => ['class' => ['govuk-heading-m']],
    ];
    $form['partnership_actions'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => [],
      '#attributes' => ['class' => ['govuk-list', 'govuk-form-group']],
    ];

    // Enforcement notification link.
    try {
      $enforcement_notice_link = $this->getFlowNegotiator()->getFlow('raise_enforcement')->getStartLink();
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($enforcement_notice_link)) {
      $options = ['attributes' => ['class' => ['raise-enforcement']]];
      $form['partnership_actions']['#items'][] = [
        '#type' => 'link',
        '#title' => $this->t("Send a notification of a proposed enforcement action"),
        '#url' => $enforcement_notice_link->getUrl(),
        '#options' => $options,
        '#weight' => 100
      ];
    }

    // Deviation request link.
    try {
      $deviation_request_link = $this->getFlowNegotiator()->getFlow('deviation_request')->getStartLink();
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($deviation_request_link)) {
      $options = ['attributes' => ['class' => ['raise-deviation-request']]];
      $form['partnership_actions']['#items'][] = [
        '#type' => 'link',
        '#title' => $this->t("Request to deviate from the inspection plan"),
        '#url' => $deviation_request_link->getUrl(),
        '#options' => $options,
        '#weight' => 100,
      ];
    }

    // Inspection feedback link.
    try {
      $inspection_feedback_link = $this->getFlowNegotiator()->getFlow('inspection_feedback')->getStartLink();
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($inspection_feedback_link)) {
      $options = ['attributes' => ['class' => ['raise-inspection-feedback']]];
      $form['partnership_actions']['#items'][] = [
        '#type' => 'link',
        '#title' => $this->t("Submit feedback following an inspection"),
        '#url' => $inspection_feedback_link->getUrl(),
        '#options' => $options,
        '#weight' => 100,
      ];
    }

    // General enquiry link.
    try {
      $general_enquiry_link = $this->getFlowNegotiator()->getFlow('enquiry')->getStartLink();
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($general_enquiry_link)) {
      $options = ['attributes' => ['class' => ['send-general-enquiry']]];
      $form['partnership_actions']['#items'][] = [
        '#type' => 'link',
        '#title' => $this->t("Send a general enquiry to the primary authority"),
        '#url' => $general_enquiry_link->getUrl(),
        '#options' => $options,
        '#weight' => 100,
      ];
    }

    return $form;
  }
}
