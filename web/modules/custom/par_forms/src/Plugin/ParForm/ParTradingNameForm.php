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
  public function loadData() {
    $par_data_organisation = $this->getflowDataHandler()->getParameter('par_data_organisation');
    $trading_name_delta = $this->getflowDataHandler()->getParameter('trading_name_delta');
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
  public function getElements($form = []) {
    $form['trading_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter a trading name'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("trading_name"),
      '#description' => $this->t("<p>Sometimes companies trade under a different name to their registered, legal name. This is known as a 'trading name'. State any trading names used by the organisation.</p>"),
    ];

    return $form;
  }
}
