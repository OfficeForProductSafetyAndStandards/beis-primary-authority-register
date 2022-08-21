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
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    /* @var ParDataPartnership $par_data_partnership */
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface) {
      $this->setDefaultValuesByKey("partnership", $cardinality, $par_data_partnership);
      $partnership_legal_entities = $par_data_partnership->getPartnershipLegalEntity();
    }
    if (isset($partnership_legal_entities) && !empty ($partnership_legal_entities)) {
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
    try {
      $partnership_legal_entity_add_link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('add_field_legal_entity');
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }

    // Do not render this plugin if there is nothing to display, for example if
    // there are no partnership legal entities and the user isn't able to add a new partnership legal entity.
    if (empty($partnership_legal_entities) && (!isset($partnership_legal_entity_add_link) || !$partnership_legal_entity_add_link instanceof Link)) {
      return $form;
    }

    // Display the partnership legal entities.
    $form['partnership_legal_entities'] = [
      '#type' => 'fieldset',
      '#title' => 'Legal entities',
      '#attributes' => ['class' => ['form-group']],
    ];

    /* @var ParDataPartnershipLegalEntity $partnership_legal_entity */
    foreach ($partnership_legal_entities as $delta => $partnership_legal_entity) {

      $legal_entity = $partnership_legal_entity->getLegalEntity();

      $form['partnership_legal_entities'][$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['grid-row']],
      ];

      $form['partnership_legal_entities'][$delta]['partnership-legal-entity'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => 'column-full partnership-legal-entity'],
      ];

      $form['partnership_legal_entities'][$delta]['partnership-legal-entity']['name'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => ['class' => 'name'],
        '#value' => $legal_entity->getName(),
      ];

      $registered_number = $legal_entity->getRegisteredNumber();
      if (!empty($registered_number)) {
        $form['partnership_legal_entities'][$delta]['partnership-legal-entity']['registered_number'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => 'registered-number'],
          '#prefix' => '(',
          '#value' => $registered_number,
          '#suffix' => ')',
        ];
      }

      // Dates only shown once partnership becomes active.
      if ($partnership->isActive()) {

        // If a partnership legal entity has no start date then it is effective from the start of the partnership.
        $start_date = $partnership_legal_entity->getStartDate();
        $start_date = $start_date ?: $partnership->getApprovedDate();
        $form['partnership_legal_entities'][$delta]['partnership-legal-entity']['start_date'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => 'start-date'],
          '#prefix' => ' - ',
          '#value' => $start_date->format('d M Y'),
        ];

        // If a partnership legal entity has no end date that indicates it is effective up to the present day.
        $end_date = $partnership_legal_entity->getEndDate();
        $end_date = (empty($end_date)) ? 'present' : $end_date->format('d M Y');
        $form['partnership_legal_entities'][$delta]['partnership-legal-entity']['end_date'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => 'end-date'],
          '#prefix' => ' - ',
          '#value' => $end_date,
        ];
      }

      $type = $legal_entity->getType();
      if (!empty($type)) {
        $form['partnership_legal_entities'][$delta]['partnership-legal-entity']['type'] = [
          '#type' => 'html_tag',
          '#tag' => 'span',
          '#attributes' => ['class' => 'type'],
          '#prefix' => '<br/>',
          '#value' => $type,
        ];
      }

    }

    // Display a link to add a legal entity.
    if (isset($partnership_legal_entity_add_link) && $partnership_legal_entity_add_link instanceof Link) {
      $link_label = !empty($partnership_legal_entities) && count($partnership_legal_entities) >= 1
        ? "add another legal entity" : "add a legal entity";
      $form['partnership_legal_entities']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $partnership_legal_entity_add_link->setText($link_label)->toString(),
        '#attributes' => ['class' => ['add-partnership-legal-entity']],
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
