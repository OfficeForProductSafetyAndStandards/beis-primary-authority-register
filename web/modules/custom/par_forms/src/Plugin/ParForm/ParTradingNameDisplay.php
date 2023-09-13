<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation');
    if (!$par_data_organisation && $par_data_partnership instanceof ParDataEntityInterface) {
      $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
    }
    if ($par_data_organisation) {
      // Get the trading names.
      $trading_names = $par_data_organisation->extractValues('trading_name');
      $this->setDefaultValuesByKey("trading_names", $index, $trading_names);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $trading_names = $this->getDefaultValuesByKey('trading_names', $index, []);

    // Generate the add a trading name link.
    try {
      $trading_name_add_link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('add_trading_name');
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }

    // Do not render this plugin if there is nothing to display, for example if
    // there are no trading names and the user isn't able to add a new trading name.
    if (empty($trading_names) && (!isset($trading_name_add_link) || !$trading_name_add_link instanceof Link)) {
      return $form;
    }

    // Display the trading_names.
    $form['trading_names'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Trading names'),
      ],
      '#attributes' => ['class' => ['form-group']],
      'trading_name' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-row']],
      ],
    ];

    foreach ($trading_names as $delta => $trading_name) {
      $form['trading_names']['trading_name'][$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => 'govuk-grid-column-full'],
        'name' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
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
        $operations['edit'] = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('edit_trading_name', 'edit trading name', $params, $options);
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }
      try {
        // Remove the trading name.
        $params['trading_name_delta'] = $delta;
        $options = ['attributes' => ['aria-label' => $this->t("Remove the trading name @label", ['@label' => strtolower($trading_name)])]];
        $operations['remove'] = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('remove_trading_name', 'remove trading name', $params, $options);

      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }

      // Display operation links if any are present.
      if (!empty(array_filter($operations))) {
        $form['trading_names']['trading_name'][$delta]['operations'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['govuk-grid-row']],
        ];
        if (isset($operations['edit']) && $operations['edit'] instanceof Link) {
          $form['trading_names']['trading_name'][$delta]['operations']['edit'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $operations['edit']->toString(),
            '#attributes' => ['class' => ['edit-trading-name', 'govuk-grid-column-one-third']],
          ];
        }
        if (isset($operations['remove']) && $operations['remove'] instanceof Link) {
          $form['trading_names']['trading_name'][$delta]['operations']['remove'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $operations['remove']->toString(),
            '#attributes' => ['class' => ['remove-trading-name', 'govuk-grid-column-one-third']],
          ];
        }
      }
    }

    // Add a link to add a trading name.
    if (isset($trading_name_add_link) && $trading_name_add_link instanceof Link) {
      $add_link_label = !empty($trading_names) && count($trading_names) >= 1
        ? "add another trading name" : "add a trading name";
      $form['trading_names']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $trading_name_add_link->setText($add_link_label)->toString(),
        '#attributes' => ['class' => ['add-trading-name']],
      ];
    }

    return $form;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($index = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
