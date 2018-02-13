<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
      $trading_name = $par_data_organisation ? $par_data_organisation->get('trading_name')->get($trading_name_delta) : NULL;

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
      $form['legal_entity_intro_fieldset'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('What is a legal entity?'),
        'intro' => [
          '#type' => 'markup',
          '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
        ]
      ];
    }

    $form['trading_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter a trading name'),
      '#default_value' => $this->getDefaultValuesByKey('trading_name', $cardinality),
      '#description' => $this->t("<p>Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State the primary trading name used by the organisation. More can be added after confirming the partnership.</p>"),
    ];

    return $form;
  }
}
