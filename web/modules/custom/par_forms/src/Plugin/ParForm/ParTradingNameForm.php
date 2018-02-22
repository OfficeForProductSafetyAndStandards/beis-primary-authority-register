<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\StringTranslation\StringTranslationTrait;
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
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'trading_name' => 'trading_name',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    $trading_name_delta = $this->getFlowDataHandler()->getParameter('trading_name_delta');
    if ($par_data_organisation) {
      // Store the current value of the trading name if it's being edited.
      $index = $trading_name_delta ?: $cardinality-1;
      $trading_name = $par_data_organisation ? $par_data_organisation->get('trading_name')->get($index) : NULL;
      if ($trading_name) {
        $this->getFlowDataHandler()->setFormPermValue("trading_name", $trading_name->getString());
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    if ($cardinality === 1) {
      // If this plugin is being added as a single item then we can explain more will be added later.
      $message = $this->formatPlural($this->getCardinality(),
        "Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State the primary trading name used by the organisation. More can be added later.",
        "Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State any trading names used by the organisation.");

      $form['trading_name_intro_fieldset'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('What is a trading name?'),
        'intro' => [
          '#type' => 'markup',
          '#markup' => $message,
          '#prefix' => "<p>",
          '#suffix' => "</p>",
        ]
      ];
    }

    $form['trading_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter a trading name'),
      '#default_value' => $this->getDefaultValuesByKey('trading_name', $cardinality),
    ];

    return $form;
  }
}
