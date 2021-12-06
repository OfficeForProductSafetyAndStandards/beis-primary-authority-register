<?php

namespace Drupal\par_enforcement_remove_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\Plugin\ParForm\ParConfirmRemoval;
use Drupal\par_forms\Plugin\ParForm\ParNotificationFollowup;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementRemovalConfirmForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = "Notice of enforcement actions | Review";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $this->getFlowDataHandler()->setFormPermValue('enforcement_name', $par_data_enforcement_notice->label());

    $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();
    $action_labels = $this->parDataManager->getEntitiesAsOptions($par_data_enforcement_actions);
    $this->getFlowDataHandler()->setFormPermValue('actions_names', $action_labels);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $enforcement_name = $this->getFlowDataHandler()->getFormPermValue('enforcement_name');
    $action_labels = $this->getFlowDataHandler()->getFormPermValue('actions_names');


    $form['summary'] = [
      '#title' => $this->t('Do you want to remove this?'),
      '#type' => 'fieldset',
      '#weight' => -99,
      '#attributes' => ['class' => ['form-group']],
      'message' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("You are about to remove the %entity and all the associated actions:", [
          '%entity' => $enforcement_name,
        ]),
      ],
      'actions' => [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#items' => $action_labels,
        '#attributes' => ['class' => ['list', 'list-bullet']],
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();

    $cid_reason = $this->getFlowNegotiator()->getFormKey('par_enforcement_remove');
    $reason = $this->getFlowDataHandler()->getDefaultValues('reason', '', $cid_reason) . PHP_EOL . PHP_EOL;
    if ($description = $this->getFlowDataHandler()->getDefaultValues('reason_description', NULL, $cid_reason)) {
      $reason .= $description;
    }

    // Remove the main enforcement notice.
    if ($par_data_enforcement_notice->destroy($reason)) {
      // Remove the enforcement actions as well.
      foreach ($par_data_enforcement_actions as $par_data_enforcement_action) {
        $par_data_enforcement_action->destroy($reason);
      }

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The enforcement notice %enforcement could not be deleted for %form_id');
      $replacements = [
        '%enforcement' => $par_data_enforcement_notice->id(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }
}
