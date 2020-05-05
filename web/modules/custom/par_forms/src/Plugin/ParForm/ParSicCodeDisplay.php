<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Organisation trading name display.
 *
 * @ParForm(
 *   id = "trading_name_display",
 *   title = @Translation("Trading name display.")
 * )
 */
class ParTradingNameDisplay extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    if (!$par_data_organisation && $par_data_partnership instanceof ParDataEntityInterface) {
      $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
    }
    if ($par_data_organisation) {
      // Get the trading names.
      $trading_names = $par_data_organisation->extractValues('trading_name');
      $this->setDefaultValuesByKey("trading_names", $cardinality, $trading_names);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Display the trading_names.
    $form['trading_names'] = [
      '#type' => 'fieldset',
      '#title' => 'Trading names',
      'trading_name' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['grid-row']],
      ],
    ];

    $trading_names = $this->getDefaultValuesByKey('trading_names', $cardinality, []);
    foreach ($trading_names as $delta => $trading_name) {
      $form['trading_names']['trading_name'][$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => 'column-full'],
        'name' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $trading_name,
          '#attributes' => ['class' => 'trading-name'],
        ],
      ];

      // Edit the trading name.
      try {
        $params['trading_name_delta'] = $delta;
        $trading_name_edit_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_trading_name', $params, [], TRUE);
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }
      if (isset($trading_name_edit_link)) {
        $form['trading_names']['trading_name'][$delta]['edit'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $trading_name_edit_link->setText($this->t("edit @trading_name", ['@trading_name' => strtolower($trading_name)]))->toString(),
          '#attributes' => ['class' => 'edit-trading-name'],
        ];
      }
    }

    // Add a link to add a trading name.
    $trading_name_add_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('add_trading_name', [], [], TRUE);
    try {
      $trading_name_add_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('add_trading_name', [], [], TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($trading_name_add_link)) {
      $form['trading_names']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $trading_name_add_link->setText("add trading name")->toString(),
        '#attributes' => ['class' => ['add-trading-name']],
      ];
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
