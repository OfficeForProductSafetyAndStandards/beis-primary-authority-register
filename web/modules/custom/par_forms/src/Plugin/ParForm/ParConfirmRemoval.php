<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  #[\Override]
  public function loadData(int $index = 1): void {
    $item_name = $this->getConfiguration()['item'] ?? 'Item';
    $this->getFlowDataHandler()->setFormPermValue("item_name", $item_name);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $item = $this->getFlowDataHandler()->getFormPermValue("item_name");

    $form['confirm'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Are you sure you want to remove this @item?', ['@item' => strtolower((string) $item)]),
      '#return_value' => self::REMOVAL_CONFIRM,
      '#wrapper_attributes' => ['class' => ['govuk-!-margin-bottom-4']],
    ];

    $form['warning'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => ['class' => ['govuk-warning-text']],
      'icon' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => '!',
        '#attributes' => [
          'class' => ['govuk-warning-text__icon'],
          'aria-hidden' => 'true',
        ],
      ],
      'strong' => [
        '#type' => 'html_tag',
        '#tag' => 'strong',
        '#value' => $this->t('This will be permanently removed, you will not be able to retrieve it afterwards.'),
        '#attributes' => ['class' => ['govuk-warning-text__text']],
        'message' => [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#value' => $this->t('Warning'),
          '#attributes' => ['class' => ['govuk-warning-text__assistive']],
        ],
      ]
    ];

    return $form;
  }

  /**
   * Validate checkbox.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    if (!$form_state->getValue('confirm') ||
      $form_state->getValue('confirm') != ParConfirmRemoval::REMOVAL_CONFIRM) {
      $id = $this->getElementId('confirm', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('You must confirm you wish to remove this item.', $id));
    }

    parent::validate($form, $form_state, $index, $action);
  }
}
