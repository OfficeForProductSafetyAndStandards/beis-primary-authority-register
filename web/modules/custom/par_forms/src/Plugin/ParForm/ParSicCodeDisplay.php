<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Organisation SIC code display.
 *
 * @ParForm(
 *   id = "sic_code_display",
 *   title = @Translation("SIC code display.")
 * )
 */
class ParSicCodeDisplay extends ParFormPluginBase {

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
      // Get the SIC Codes.
      $sic_codes = $par_data_organisation->get('field_sic_code')->referencedEntities();
      $this->setDefaultValuesByKey("sic_codes", $cardinality, $sic_codes);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Display the trading_names.
    $form['sic_codes'] = [
      '#type' => 'fieldset',
      '#title' => 'Standard industrial classification (SIC) codes',
      '#attributes' => ['class' => ['form-group']],
      'sic_code' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['grid-row']],
      ],
    ];

    $sic_codes = $this->getDefaultValuesByKey('sic_codes', $cardinality, []);
    foreach ($sic_codes as $delta => $sic) {
      // If the sic is not an entity display the value as is.
      $id = $sic instanceof ParDataEntityInterface ? $sic->id() : $sic;
      $label = $sic instanceof ParDataEntityInterface ? $sic->label() : $sic;

      $form['sic_codes']['sic_code'][$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => 'column-full'],
        'code' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $label,
          '#attributes' => ['class' => 'sic-code'],
        ],
      ];

      // Generate item operation links.
      $operations = [];
      try {
        // Edit the SIC code.
        $params = ['field_sic_code_delta' => $delta];
        $options = ['attributes' => ['aria-label' => $this->t("Edit the SIC code @label", ['@label' => strtolower($label)])]];
        $operations['edit'] = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_field_sic_code', $params, $options, TRUE);
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }
      try {
        // Remove the SIC code.
        $params = ['field_sic_code_delta' => $delta];
        $options = ['attributes' => ['aria-label' => $this->t("Remove the SIC code @label", ['@label' => strtolower($label)])]];
        $operations['remove'] = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('remove_field_sic_code', $params, $options, TRUE);
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }

      // Display operation links if any are present.
      if (!empty(array_filter($operations))) {
        $form['sic_codes']['sic_code'][$delta]['operations'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['grid-row']],
        ];
        if (isset($operations['edit']) && $operations['edit'] instanceof Link) {
          $form['sic_codes']['sic_code'][$delta]['operations']['edit'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $operations['edit']->setText("edit sic code")->toString(),
            '#attributes' => ['class' => ['edit-sic-code', 'column-one-third']],
          ];
        }
        if (isset($operations['remove']) && $operations['remove'] instanceof Link) {
          $form['sic_codes']['sic_code'][$delta]['operations']['remove'] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $operations['remove']->setText("remove sic code")->toString(),
            '#attributes' => ['class' => ['remove-sic-code', 'column-one-third']],
          ];
        }
      }
    }

    // Add a link to add a new sic code.
    try {
      $sic_code_add_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('add_field_sic_code', [], [], TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($sic_code_add_link) && $sic_code_add_link instanceof Link) {
      $form['sic_codes']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $sic_code_add_link->setText("add sic code")->toString(),
        '#attributes' => ['class' => ['add-sic-code']],
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
