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
 * Confirm removal of an entity form plugin.
 *
 * @ParForm(
 *   id = "confirm_removal",
 *   title = @Translation("Confirm the removal.")
 * )
 */
class ParConfirmRemoval extends ParFormPluginBase {

  /**
   * The value for the policy confirmation.
   */
  const REMOVAL_CONFIRM = 'remove';

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $item_name = $this->getConfiguration()['item'] ?? 'Item';
    $this->getFlowDataHandler()->setFormPermValue("item_name", $item_name);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $item = $this->getFlowDataHandler()->getFormPermValue("item_name");

    $form['confirm'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Are you sure you want to remove this @item?', ['@item' => strtolower($item)]),
      '#description' => $this->t('This will be permanently removed, you will not be able to retrieve it afterwards.'),
      '#return_value' => self::REMOVAL_CONFIRM,
      '#attributes' => ['class' => ['form-group']],
    ];

    return $form;
  }

  /**
   * Validate checkbox.
   */
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    if (!$form_state->getValue('confirm') ||
      $form_state->getValue('confirm') != ParConfirmRemoval::REMOVAL_CONFIRM) {
      $id = $this->getElementId('confirm', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('You must confirm you wish to remove this item.', $id));
    }

    parent::validate($form, $form_state, $cardinality, $action);
  }
}