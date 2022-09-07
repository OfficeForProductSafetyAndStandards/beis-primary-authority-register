<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipLegalEntity;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Partnership Legal entity display.
 *
 * @ParForm(
 *   id = "partnership_legal_entity_display",
 *   title = @Translation("Partnership Legal Entity display.")
 * )
 */
class ParPartnershipLegalEntityDisplay extends ParFormPluginBase {

  /**
   * Return the date formatter service.
   *
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    /* @var ParDataPartnership $par_data_partnership */
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface) {
      $this->setDefaultValuesByKey("partnership", $cardinality, $par_data_partnership);
      $partnership_legal_entities = $par_data_partnership->getPartnershipLegalEntity();
      $this->setDefaultValuesByKey("partnership_legal_entities", $cardinality, $partnership_legal_entities);
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    /* @var ParDataPartnership $partnership */
    $partnership = $this->getDefaultValuesByKey('partnership', $cardinality, []);
    $partnership_legal_entities = $this->getDefaultValuesByKey('partnership_legal_entities', $cardinality, []);

    // Generate the link to add a new partnership legal entity.
    $link_label = !empty($partnership_legal_entities) && count($partnership_legal_entities) >= 1
      ? "add another legal entity" : "add a legal entity";
    try {
      $add_link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('add_field_legal_entity', $link_label, ['par_data_partnership' => $partnership]);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }

    // Do not render this plugin if there is nothing to display, for example if
    // there are no partnership legal entities and the user isn't able to add a new partnership legal entity.
    if (empty($partnership_legal_entities) && !$add_link instanceof Link) {
      return $form;
    }

    // Fieldset encompassing the partnership legal entities plugin display.
    $form['partnership_legal_entities'] = [
      '#type' => 'fieldset',
      '#title' => 'Legal entities',
      '#attributes' => ['class' => ['form-group']],
    ];

    // Display a link to add a legal entity. Weighted to sink to bottom.
    if ($add_link instanceof Link) {
      $form['partnership_legal_entities_x']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $add_link->toString(),
        '#attributes' => ['class' => ['add-partnership-legal-entity']],
        '#weight' => 99,
      ];
    }

    // If there are currently no partnership legal entities to show we can stop here, no need to show an empty table.
    if (empty($partnership_legal_entities)) {
      return $form;
    }

    // Table in which to show partnership legal entities.
    // We only show start/end date columns for active partnerships.
    // @note We should hide the operations column if user has no access to any operations.
    if ($partnership->isActive()) {
      $form['partnership_legal_entities']['table'] = [
        '#type' => 'table',
        '#header' => [
          'Name',
          'Start date',
          'End date',
          'Operations',
        ],
      ];
    }
    else {
      $form['partnership_legal_entities']['table'] = [
        '#type' => 'table',
        '#header' => [
          'Name',
          'Operations',
        ],
      ];
    }

    // Add a row for each partnership legal entity.
    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    foreach ($partnership_legal_entities as $delta => $partnership_legal_entity) {

      // Get the actual legal entity instance.
      $legal_entity = $partnership_legal_entity->getLegalEntity();

      // The LE name goes in the first 'identity' column.
      $form['partnership_legal_entities']['table'][$delta]['identity']['name'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => ['class' => 'name'],
        '#value' => $legal_entity->getName(),
      ];

      // If we have one the LE registered number also goes in the 'identity' column.
      $registered_number = $legal_entity->getRegisteredNumber();
      if (!empty($registered_number)) {
        $form['partnership_legal_entities']['table'][$delta]['identity']['registered_number'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => 'registered-number'],
          '#prefix' => '(',
          '#value' => $registered_number,
          '#suffix' => ')',
        ];
      }

      // If we have one the LE type goes in the 'identity' column but below the name.
      $type = $legal_entity->getType();
      if (!empty($type)) {
        $form['partnership_legal_entities']['table'][$delta]['identity']['type'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => 'type'],
          '#prefix' => '<br/>',
          '#value' => $type,
        ];
      }

      // Date columns only present once partnership becomes active.
      if ($partnership->isActive()) {

        // Start date cell is empty if the is no start date. LE is effective from the start of the partnership.
        $start_date = $partnership_legal_entity->getStartDate();
        if ($start_date) {
          $form['partnership_legal_entities']['table'][$delta]['start_date'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => ['class' => 'start-date'],
            '#value' => $this->getDateFormatter()->format($start_date->getTimestamp(), 'gds_date_format'),
          ];
        }
        else {
          $form['partnership_legal_entities']['table'][$delta]['start_date'] = [];
        }

        // End date cell is empty if the is no end date. LE is effective to present day.
        $end_date = $partnership_legal_entity->getEndDate();
        if ($end_date) {
          $form['partnership_legal_entities']['table'][$delta]['end_date'] = [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => ['class' => 'end-date'],
            '#value' => $this->getDateFormatter()->format($end_date->getTimestamp(), 'gds_date_format'),
          ];
        }
        else {
          $form['partnership_legal_entities']['table'][$delta]['end_date'] = [];
        }
      }

      // Operation links will go in the last column.
      $operations = [
        'edit_field_legal_entity' => 'Edit',
        'revoke_field_legal_entity' => 'Revoke',
        'reinstate_field_legal_entity' => 'Reinstate',
        'remove_field_legal_entity' => 'Remove',
      ];
      $link_params = [
        'par_data_partnership' => $partnership,
        'par_data_partnership_le' => $partnership_legal_entity,
      ];
      foreach ($operations as $link_name => $link_label) {

        // Attempt to generate the link.
        try {
          $link = $this->getFlowNegotiator()->getFlow()->getOperationLink($link_name, $link_label, $link_params);
        }
        catch (ParFlowException $e) {
          $this->getLogger($this->getLoggerChannel())->notice($e);
          continue;
        }

        // Display the link.
        if ($link instanceof Link) {
          $form['partnership_legal_entities']['table'][$delta]['operations'][$link_name] = [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $link->toString(),
            '#attributes' => ['class' => [$link_name]],
          ];
        }

      }
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
