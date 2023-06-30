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
use Drupal\registered_organisations\DataException;
use Drupal\registered_organisations\RegisterException;
use Drupal\registered_organisations\TemporaryException;

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
      $partnership_legal_entities = $par_data_partnership->getPartnershipLegalEntities();
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
    /* @var ParDataPartnershipLegalEntity[] $partnership_legal_entities */
    $partnership_legal_entities = $this->getDefaultValuesByKey('partnership_legal_entities', $cardinality, []);

    // Generate the link to add a new partnership legal entity.
    try {
      $link_label = !empty($partnership_legal_entities) && count($partnership_legal_entities) >= 1
        ? "add another legal entity" : "add a legal entity";
      $add_link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('add_legal_entity', $link_label, ['par_data_partnership' => $partnership]);
    }
    catch (ParFlowException $e) {
      $this->getLogger($this->getLoggerChannel())->notice($e);
    }

    // Fieldset encompassing the partnership legal entities plugin display.
    $form['partnership_legal_entities'] = [
      '#type' => 'fieldset',
      '#title' => 'Legal entities',
      '#attributes' => ['class' => ['form-group']],
    ];

    // Display a link to add a legal entity. Weighted to sink to bottom.
    if (isset($add_link) && $add_link instanceof Link) {
      $form['partnership_legal_entities']['add'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $add_link->toString(),
        '#attributes' => ['class' => ['add-partnership-legal-entity']],
        '#weight' => 99,
      ];
    }

    // Do not render table if no legal entities to display.
    if (empty($partnership_legal_entities)) {
      $form['partnership_legal_entities']['no_results'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("There are no legal entities covered by this partnership."),
      ];
      return $form;
    }

    // Table in which to show partnership legal entities.
    $form['partnership_legal_entities']['table'] = [
      '#type' => 'table',
      '#header' => [
        'Name',
        'Status',
      ],
    ];
    $headers = &$form['partnership_legal_entities']['table']['#header'];

    // Add a row for each partnership legal entity.
    foreach ($partnership_legal_entities as $delta => $partnership_legal_entity) {
      // Get the actual legal entity instance.
      $legal_entity = $partnership_legal_entity->getLegalEntity();

      // @TODO Remove updateLegacyEntities() once the majority of legacy legal entities are updated.
      if ($legal_entity?->isLegacyEntity()) {
        try {
          $updated = $legal_entity->updateLegacyEntities();
          if ($updated) {
            $legal_entity->save();
          }
        }
        catch (RegisterException|TemporaryException|DataException $ignored) {
          // Catch all errors silently.
        }
      }

      // Get all the operations available for this legal entity.
      $operations = [
        'revoke_legal_entity' => 'Revoke',
        'reinstate_legal_entity' => 'Reinstate',
        'remove_legal_entity' => 'Remove',
      ];
      $link_params = [
        'par_data_partnership' => $partnership,
        'par_data_partnership_le' => $partnership_legal_entity,
      ];
      $legal_entity_actions = [];
      foreach ($operations as $link_name => $link_label) {
        // Attempt to generate the link.
        try {
          $link = $this->getFlowNegotiator()->getFlow()->getOperationLink($link_name, $link_label, $link_params);
          $legal_entity_actions[$link_name] = $link;
        }
        catch (ParFlowException $e) {
          $this->getLogger($this->getLoggerChannel())->notice($e);
          continue;
        }
      }

      // Only show the operations column if user has access to modify the legal entities.
      if (!empty($legal_entity_actions) && array_search('Actions', $headers) === false) {
        $headers[3] = 'Actions';
      }
      $legal_entity_view_builder = $this->getParDataManager()->getViewBuilder('par_data_legal_entity');
      $legal_entity_summary = $legal_entity_view_builder->view($legal_entity, 'summary');
      $classes = ['legal-entity'];

      $form['partnership_legal_entities']['table'][$delta]['legal_entity'] = [
        '#type' => 'container',
        'name' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $this->getRenderer()->render($legal_entity_summary),
          '#attributes' => ['class' => $classes],
        ],
      ];

      // Check if this legal entity needs updating.
      if ($legal_entity->isLegacyEntity()) {
        $legacy_message = "This legal entity needs to be updated.";
        $form['partnership_legal_entities']['table'][$delta]['legal_entity']['warning'] = [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $legacy_message,
          '#attributes' => ['class' => ['govuk-warning-text']],
        ];
      }

      // Get the status message to display.
      $status_message = "@status";

      // Get the start and end dates.
      $start_date = $partnership_legal_entity->getStartDate()?->getTimestamp();
      $end_date = $partnership_legal_entity->getEndDate()?->getTimestamp();

      if ($partnership_legal_entity->isActive() && $start_date) {
        // Add the date the legal entity was nominated.
        $status_message .= "<br>@start to present";
      }
      else if ($partnership_legal_entity->isRevoked() && $start_date && $end_date) {
        // Add the dates the legal entity was active during.
        $status_message .= "<br>@start to @end";
      }

      // Get the replacement values.
      $status = $partnership_legal_entity->getParStatus();
      $start = $start_date ? $this->getDateFormatter()->format($start_date, 'gds_date_format') : NULL;
      $end = $end_date ? $this->getDateFormatter()->format($end_date, 'gds_date_format') : NULL;

      $form['partnership_legal_entities']['table'][$delta]['status'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => ['class' => 'status'],
        '#value' => $this->t($status_message, ['@status' => $status, '@start' => $start, '@end' => $end]),
      ];

      if (!empty($legal_entity_actions)) {
        // Operation links will go in the last column.
        $form['partnership_legal_entities']['table'][$delta]['operations'] = [];

        // Display the link.
        foreach ($legal_entity_actions as $link_name => $link) {
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
