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
 * Partnership information display.
 *
 * @ParForm(
 *   id = "partnership_information_display",
 *   title = @Translation("Partnership information display.")
 * )
 */
class ParPartnershipInformationDisplay extends ParFormPluginBase {

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface) {
      // Format the date.
      if ($par_data_partnership->hasField('approved_date')) {
        $date = $par_data_partnership->approved_date->view('full');
        $this->setDefaultValuesByKey("date", $cardinality, $date);
      }

      // Get the authority name.
      if ($par_data_authority = $par_data_partnership->getAuthority(TRUE)) {
        $this->setDefaultValuesByKey("name", $cardinality, $par_data_authority->getName());
      }

      // Get the partnership information.
      if ($par_data_partnership->hasField('about_partnership')) {
        $information_display = $par_data_partnership->about_partnership->view('full');
        $this->setDefaultValuesByKey("information", $cardinality, $information_display);
      }

      // Get the regulatory functions.
      if ($par_data_partnership->hasField('field_regulatory_function')) {
        $regulatory_functions = $par_data_partnership->get('field_regulatory_function')->referencedEntities();
        $regulatory_function_labels = $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions);
        $this->setDefaultValuesByKey("regulatory_functions", $cardinality, $regulatory_function_labels);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Partnership Authority Name - component.
    $form['authority_name'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "<span class='heading-secondary'>In partnership with</span>" . $this->getDefaultValuesByKey('name', $cardinality, NULL),
      '#attributes' => ['class' => ['heading-large', 'form-group', 'authority-name']],
    ];


    // Partnership Basic Information - component.
    $form['partnership_info'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Display details about the partnership for information.
    $form['partnership_info']['about_partnership'] = [
      '#type' => 'fieldset',
      '#title' => 'About the partnership',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'details' => $this->getDefaultValuesByKey('information', $cardinality, NULL),
    ];
    try {
      $about_edit_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_about_partnership', [], [], TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($about_edit_link)) {
      $form['partnership_info']['about']['edit'] = [
        '#type' => 'markup',
        '#markup' => '<p>' . $about_edit_link->setText("edit about the partnership")->toString() . '</p>',
      ];
    }

    // Display the regulatory functions and partnership approved date.
    $form['partnership_info']['details'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['grid-row']],
      'regulatory_functions' => [
        '#type' => 'fieldset',
        '#title' => 'Partnered for',
        '#attributes' => ['class' => 'column-one-half'],
        'value' => [
          '#theme' => 'item_list',
          '#list_type' => 'ul',
          '#items' => $this->getDefaultValuesByKey('regulatory_functions', $cardinality, NULL),
        ]
      ],
      'approved_date' => [
        '#type' => 'fieldset',
        '#title' => 'In partnership since',
        '#attributes' => ['class' => 'column-one-half'],
        'value' => $this->getDefaultValuesByKey('date', $cardinality, NULL),
      ],
    ];
    try {
      $regulatory_functions_edit_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_regulatory_functions', [], [], TRUE);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($regulatory_functions_edit_link)) {
      $form['partnership_info']['details']['edit'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $regulatory_functions_edit_link->setText("edit the regulatory functions")->toString(),
        '#attributes' => ['class' => 'column-full'],
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
