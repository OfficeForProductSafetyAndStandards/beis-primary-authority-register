<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
   * The number of legal entity items to display at any one time.
   */
  const NUMBER_ITEMS = 5;

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    /* @var ParDataPartnership $par_data_partnership */
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership instanceof ParDataEntityInterface) {
      $this->setDefaultValuesByKey("partnership", $index, $par_data_partnership);
      $partnership_legal_entities = $par_data_partnership->getPartnershipLegalEntities();
      $this->setDefaultValuesByKey("partnership_legal_entities", $index, $partnership_legal_entities);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    /* @var ParDataPartnership $partnership */
    $partnership = $this->getDefaultValuesByKey('partnership', $index, []);
    /* @var ParDataPartnershipLegalEntity[] $partnership_legal_entities */
    $partnership_legal_entities = $this->getDefaultValuesByKey('partnership_legal_entities', $index, []);

    // Get the unique pager for this component.
    $pager = $this->getUniquePager()->getPager('partnership_legal_entities');
    $count = count($partnership_legal_entities);
    $current_pager = $this->getUniquePager()->getPagerManager()->createPager($count, self::NUMBER_ITEMS, $pager);

    // Split the members up into chunks.
    $chunks = array_chunk($partnership_legal_entities, self::NUMBER_ITEMS);
    // The current chunk to display.
    $chunk = $chunks[$current_pager->getCurrentPage()] ?? [];

    $route_params = ['par_data_partnership' => $partnership];
    $actions = [];
    // Generate the link to add a new partnership legal entity for pending partnerships.
    try {
      $link_label = !empty($partnership_legal_entities) && count($partnership_legal_entities) >= 1
        ? "Add another legal entity" : "Add a legal entity";
      $link_options = [ 'attributes' => ['class' => ['add-action']] ];
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('add_legal_entity', $link_label, $route_params, $link_options);
      if ($link instanceof Link) {
        $actions['add'] = $link;
      }
    }
    catch (ParFlowException $ignored) {

    }
    // Generate the partnership amendment link for active partnerships.
    try {
      $link_options = [ 'attributes' => ['class' => ['amend-partnership-action']] ];
      $link = $this->getFlowNegotiator()->getFlow('amend_partnership')
        ->getStartLink(1, "Amend the legal entities", $route_params, $link_options);
      if ($link instanceof Link) {
        $actions['amend'] = $link;
      }
    }
    catch (ParFlowException $ignored) {

    }
    // Generate the partnership amendment confirmation link for active partnerships.
    try {
      $link_options = [ 'attributes' => ['class' => ['confirm-amendment-action']] ];
      $link = $this->getFlowNegotiator()->getFlow('confirm_partnership_amendment')
        ->getStartLink(1, "Confirm the amendments", $route_params, $link_options);
      if ($link instanceof Link) {
        $actions['amend_confirm'] = $link;
      }
    }
    catch (ParFlowException $ignored) {

    }
    // Generate the partnership amendment nomination link for active partnerships.
    try {
      $link_options = [ 'attributes' => ['class' => ['nominate-amendment-action']] ];
      $link = $this->getFlowNegotiator()->getFlow('nominate_partnership_amendment')
        ->getStartLink(1, "Nominate the amendments", $route_params, $link_options);
      if ($link instanceof Link) {
        $actions['amend_nominate'] = $link;
      }
    }
    catch (ParFlowException $ignored) {

    }

    // Fieldset encompassing the partnership legal entities plugin display.
    $form['partnership_legal_entities'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['form-group']],
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['heading-medium']],
        '#value' => $this->t('Legal Entities'),
      ],
      'pager' => [
        '#type' => 'pager',
        '#theme' => 'pagerer',
        '#element' => $pager,
        '#weight' => 98,
        '#config' => [
          'preset' => $this->config('pagerer.settings')
            ->get('core_override_preset'),
        ],
      ],
      'actions' => [
        '#theme' => 'item_list',
        '#attributes' => ['class' => ['list']],
        '#weight' => 99
      ],
    ];

    // Render all the component action links as a list.
    foreach ($actions as $key => $action) {
      /** @var Link $action */
      $form['partnership_legal_entities']['actions']['#items'][$key] = [
        '#type' => 'link',
        '#title' => $action->getText(),
        '#url' => $action->getUrl(),
        '#options' => $action->getUrl()->getOptions(),
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

    // Record which legal entities have actions.
    $legal_entity_actions = [];

    // Add a row for each partnership legal entity.
    foreach ($chunk as $delta => $partnership_legal_entity) {
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
        'edit_legal_entity' => 'Update',
      ];
      $link_params = [
        'par_data_partnership' => $partnership,
        'par_data_partnership_le' => $partnership_legal_entity,
      ];
      $actions = [];
      foreach ($operations as $link_name => $link_label) {
        // Attempt to generate the link.
        try {
          $link = $this->getFlowNegotiator()
            ->getFlow()
            ->getOperationLink($link_name, $link_label, $link_params);
          if ($link instanceof Link) {
            $actions[$link_name] = $link;
          }
        }
        catch (ParFlowException $e) {
          $this->getLogger($this->getLoggerChannel())->notice($e);
          continue;
        }
      }
      if (!empty($actions)) {
        $legal_entity_actions[$delta] = $actions;
      }

      $legal_entity_view_builder = $this->getParDataManager()
        ->getViewBuilder('par_data_legal_entity');
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
      else {
        if ($partnership_legal_entity->isRevoked() && $start_date && $end_date) {
          // Add the dates the legal entity was active during.
          $status_message .= "<br>@start to @end";
        }
      }

      // Get the replacement values.
      $status = $partnership_legal_entity->getParStatus();
      $start = $start_date ? $this->getDateFormatter()
        ->format($start_date, 'gds_date_format') : NULL;
      $end = $end_date ? $this->getDateFormatter()
        ->format($end_date, 'gds_date_format') : NULL;

      $form['partnership_legal_entities']['table'][$delta]['status'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => ['class' => 'status'],
        '#value' => $this->t($status_message, [
          '@status' => $status,
          '@start' => $start,
          '@end' => $end
        ]),
      ];
    }

    // Only show the operations column if user has access to modify the legal entities.
    if (!empty($legal_entity_actions) && array_search('Actions', $headers) === false) {
      $headers[3] = 'Actions';
    }

    // Add all the actions, this ensures that the action column will only be displayed
    // if there is an action to display on at least one of the legal entities.
    if (!empty($legal_entity_actions)) {
      foreach ($chunk as $delta => $legal_entity) {
        $actions = $legal_entity_actions[$delta] ?? [];
        // Operation links will go in the last column.
        if (array_search('Actions', $headers) !== FALSE) {
          $form['partnership_legal_entities']['table'][$delta]['operations'] = ['#type' => 'container'];
        }

        // Display the link.
        foreach ($actions as $link_name => $link) {
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
