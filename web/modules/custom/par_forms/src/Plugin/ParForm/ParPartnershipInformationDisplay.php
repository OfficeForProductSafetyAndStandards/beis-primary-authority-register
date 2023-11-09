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
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface) {
      // Is the partnership approved?
      $this->setDefaultValuesByKey("approved", $index, $par_data_partnership->isActive());

      // Set the partnership type.
      $this->setDefaultValuesByKey("partnership_type", $index, $par_data_partnership->isCoordinated() ? 'Coordinated' : 'Direct');

      // Format the date.
      if ($par_data_partnership->hasField('approved_date')) {
        $date = $par_data_partnership->approved_date->view('full');
        $this->setDefaultValuesByKey("date", $index, $date);
      }

      // Get the authority name.
      if ($par_data_authority = $par_data_partnership->getAuthority(TRUE)) {
        $this->setDefaultValuesByKey("name", $index, $par_data_authority->getName());
      }

      // Get the partnership information.
      if ($par_data_partnership->hasField('about_partnership')) {
        $information_display = $par_data_partnership->about_partnership->view('full');
        $this->setDefaultValuesByKey("about_partnership", $index, $information_display);
      }

      // Get the regulatory functions.
      if ($par_data_partnership->hasField('field_regulatory_function')) {
        $regulatory_functions = $par_data_partnership->get('field_regulatory_function')->referencedEntities();
        $regulatory_function_labels = $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions);
        $this->setDefaultValuesByKey("regulatory_functions", $index, $regulatory_function_labels);
      }

      // Display the previous name, only display the last one if more than one.
      if ($par_data_partnership->hasField('previous_names')) {
        $previous_name = $par_data_partnership->getPreviousName();
        $this->setDefaultValuesByKey("previous_names", $index, $previous_name);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Partnership Authority Name - component.
    $form['names'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-form-group']],
    ];
    $form['names']['authority_name'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => "<span class='govuk-caption-l'>In partnership with</span>" . $this->getDefaultValuesByKey('name', $index, NULL),
      '#attributes' => ['class' => ['govuk-heading-l', 'authority-name']],
    ];
    if ($previous_names = $this->getDefaultValuesByKey('previous_names', $index, NULL)) {
      $form['names']['previous_names'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => "Previously known as: " . $previous_names,
        '#attributes' => ['class' => ['govuk-body-s']],
      ];
    }

    // Display details about the partnership for information.
    $form['about_partnership'] = [
      '#type' => 'container',
      '#markup' => '<h3 class="govuk-heading-m">About the partnership</h3>',
      '#attributes' => ['class' => ['govuk-form-group']],
      'details' => $this->getDefaultValuesByKey('about_partnership', $index, NULL),
    ];
    try {
      $about_edit_link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('edit_about_partnership', 'edit about the partnership');
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }
    if (isset($about_edit_link)) {
      $form['about_partnership']['edit'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $about_edit_link ? $about_edit_link->toString() : '',
        '#attributes' => ['class' => 'edit-about-partnership'],
        '#weight' => 100
      ];
    }

    // Display the regulatory functions and partnership approved date.
    if ($this->getDefaultValuesByKey('approved', $index, FALSE)) {
      $form['details'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-grid-row', 'govuk-form-group']],
        'regulatory_functions' => [
          '#type' => 'container',
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h3',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => $this->t('Partnered for'),
          ],
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'functions' => [
            '#theme' => 'item_list',
            '#list_header_tag' => 'h2',
            '#list_type' => 'ul',
            '#items' => $this->getDefaultValuesByKey('regulatory_functions', $index, []),
          ]
        ],
        'partnership_type' => [
          '#type' => 'container',
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h3',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => $this->t('Partnership type'),
          ],
          'type' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $this->getDefaultValuesByKey('partnership_type', $index, ''),
          ],
        ],
        'approved_date' => [
          '#type' => 'container',
          '#attributes' => ['class' => 'govuk-grid-column-one-half'],
          'heading' => [
            '#type' => 'html_tag',
            '#tag' => 'h3',
            '#attributes' => ['class' => ['govuk-heading-m']],
            '#value' => $this->t('In partnership since'),
          ],
          'value' => $this->getDefaultValuesByKey('date', $index, ''),
        ],
      ];

      try {
        $regulatory_functions_edit_link = $this->getFlowNegotiator()->getFlow()
          ->getOperationLink('edit_regulatory_functions', 'edit the regulatory functions');

      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }
      if (isset($regulatory_functions_edit_link)) {
        $form['details']['regulatory_functions']['edit'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $regulatory_functions_edit_link->toString(),
          '#attributes' => ['class' => ['edit-regulatory-functions']],
        ];
      }
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
