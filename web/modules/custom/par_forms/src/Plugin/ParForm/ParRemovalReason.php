<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Removal reason form plugin.
 *
 * @ParForm(
 *   id = "removal_reason",
 *   title = @Translation("What is the reason for removal.")
 * )
 */
class ParRemovalReason extends ParFormPluginBase {

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $reason_options = $this->getConfiguration()['reasons'] ?? [];
    $this->getFlowDataHandler()->setFormPermValue("reason_options", $reason_options);

    $item_name = $this->getConfiguration()['item'] ?? 'Item';
    $this->getFlowDataHandler()->setFormPermValue("item_name", $item_name);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $options = $this->getFlowDataHandler()->getFormPermValue("reason_options");
    $item = $this->getFlowDataHandler()->getFormPermValue("item_name");

    $form['reason_selection'] =  [
      '#type' => 'radios',
      '#title' => $this->t('Why are you deleting this @item', ['@item' => strtolower($item)]),
      '#description' => $this->t('There are only a select number of circumstances in which this @item can be removed.', ['@item' => strtolower($item)]),
      '#default_value' => $this->getDefaultValuesByKey('reason_selection', $cardinality),
      '#options' => $options,
      '#attributes' => ['class' => ['form-group']],
    ];

    $form['reason_description'] =  [
      '#type' => 'textarea',
      '#title' => $this->t('Please describe why this @item is being removed.', ['@item' => strtolower($item)]),
      '#attributes' => ['class' => ['form-group']],
      '#default_value' => $this->getDefaultValuesByKey('reason_description', $cardinality),
    ];

    return $form;
  }

  /**
   * Validate reason.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $reason_selection = $form_state->getValue('reason_selection');
    if (!$reason_selection) {
      $id = $this->getElementId('reason_selection', $form);
      $form_state->setErrorByName($this->getElementName(['reason_selection']), $this->wrapErrorMessage('You must select a reason for removal.', $id));
    }

    if (!$form_state->getValue('reason_description')) {
      $id = $this->getElementId('reason_description', $form);
      $form_state->setErrorByName($this->getElementName(['reason_description']), $this->wrapErrorMessage('Please give a description of why this removal is being requested.', $id));
    }

    parent::validate($form, $form_state, $cardinality, $action);
  }
}
