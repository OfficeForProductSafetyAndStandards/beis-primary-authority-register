<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
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
      '#attributes' => ['class' => ['form-group']],
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

      // Generate item operation links.
      $operations = [];
      try {
        // Edit the trading name.
        $params['trading_name_delta'] = $delta;
        $options = ['attributes' => ['aria-label' => $this->t("Edit the trading name @label", ['@label' => strtolower($trading_name)])]];
        $operations['edit'] = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_trading_name', $params, $options, TRUE);
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }
      try {
        // Remove the trading name.
        $params['trading_name_delta'] = $delta;
        $options = ['attributes' => ['aria-label' => $this->t("Remove the trading name @label", ['@label' => strtolower($trading_name)])]];
        $operations['remove'] = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('remove_trading_name', $params, $options, TRUE);
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }

      // Display operation links if any are present.
      if (!empty(array_filter($operations))) {
        $form['trading_names']['trading_name'][$delta]['operations'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['grid-row']],
        ];
        if (isset($operations['edit']) && $operations['edit'] instanceof Link) {
          $form['trading_names']['trading_name'][$delta]['operations']['edit'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $operations['edit']->setText("edit trading name")->toString(),
            '#attributes' => ['class' => ['edit-trading-name', 'column-one-third']],
          ];
        }
        if (isset($operations['remove']) && $operations['remove'] instanceof Link) {
          $form['trading_names']['trading_name'][$delta]['operations']['remove'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $operations['remove']->setText("remove trading name")->toString(),
            '#attributes' => ['class' => ['remove-trading-name', 'column-one-third']],
          ];
        }
      }
    }

    // Add a link to add a trading name.
    try {
      $link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('add_trading_name', [], [], TRUE);
      $trading_name_add_link = $link->setText("add trading name")->toString();
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($trading_name_add_link) && $trading_name_add_link instanceof Link) {
      $form['trading_names']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $trading_name_add_link,
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
