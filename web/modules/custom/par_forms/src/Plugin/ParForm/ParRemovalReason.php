<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  #[\Override]
  public function loadData(int $index = 1): void {
    $reason_options = $this->getConfiguration()['reasons'] ?? [];
    $this->getFlowDataHandler()->setFormPermValue("reason_options", $reason_options);

    $item_name = $this->getConfiguration()['item'] ?? 'Item';
    $this->getFlowDataHandler()->setFormPermValue("item_name", $item_name);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $options = $this->getFlowDataHandler()->getFormPermValue("reason_options");
    $item = $this->getFlowDataHandler()->getFormPermValue("item_name");

    $form['reason_selection'] = [
      '#type' => 'radios',
      '#title' => $this->t('Why are you deleting this @item', ['@item' => strtolower((string) $item)]),
      '#title_tag' => 'h2',
      '#description' => $this->t('There are only a select number of circumstances in which this @item can be removed.', ['@item' => strtolower((string) $item)]),
      '#default_value' => $this->getDefaultValuesByKey('reason_selection', $index),
      '#options' => $options,
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    $form['reason_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Please describe why this @item is being removed.', ['@item' => strtolower((string) $item)]),
      '#attributes' => ['class' => ['govuk-form-group']],
      '#default_value' => $this->getDefaultValuesByKey('reason_description', $index),
    ];

    return $form;
  }

  /**
   * Validate reason.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $reason_selection = $form_state->getValue('reason_selection');
    if (!$reason_selection) {
      $id = $this->getElementId('reason_selection', $form);
      $form_state->setErrorByName($this->getElementName(['reason_selection']), $this->wrapErrorMessage('You must select a reason for removal.', $id));
    }

    if (!$form_state->getValue('reason_description')) {
      $id = $this->getElementId('reason_description', $form);
      $form_state->setErrorByName($this->getElementName(['reason_description']), $this->wrapErrorMessage('Please give a description of why this removal is being requested.', $id));
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
