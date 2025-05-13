<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "trading_name",
 *   title = @Translation("Trading name form.")
 * )
 */
class ParTradingNameForm extends ParFormPluginBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['trading_name', 'par_data_organisation', 'trading_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the trading name for this organisation.',
    ],
    ],
  ];

  /**
   * {@inheritdoc}
   */
  protected string $wrapperName = 'trading name';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');

    if ($par_data_organisation && isset($trading_name_delta)) {
      // Store the current value of the trading name if it's being edited.
      $delta = $trading_name_delta ?: $index - 1;
      try {
        $trading_name = $par_data_organisation ? $par_data_organisation->get('trading_name')->get($delta) : NULL;
        if ($trading_name) {
          $this->setDefaultValuesByKey('trading_name', $delta, $trading_name->getString());
        }
      }
      catch (MissingDataException $e) {
        $message = $this->t('Trading name could not be loaded due to missing data: %error');
        $replacements = [
          '%error' => $e->getMessage(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    if ($index === 1) {
      // If this plugin is being added as a single item then we can explain more will be added later.
      $message = $this->formatPlural($this->getCardinality(),
        "Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State the primary trading name used by the organisation. More can be added later.",
        "Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State any trading names used by the organisation.");

      $form['trading_name_intro_fieldset'] = [
        '#type' => 'container',
        'heading' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#attributes' => ['class' => ['govuk-heading-m']],
          '#value' => $this->t('What is a trading name?'),
        ],
        'intro' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $message,
        ],
      ];
    }

    $form['trading_name'] = [
      '#type' => 'textfield',
      '#title' => $this->formatPlural($index, 'Enter a trading name', 'Enter an additional trading name (optional)'),
      '#default_value' => $this->getDefaultValuesByKey('trading_name', $index),
    ];

    return $form;
  }

}
