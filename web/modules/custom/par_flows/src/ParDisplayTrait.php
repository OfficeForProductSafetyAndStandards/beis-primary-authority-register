<?php

namespace Drupal\par_flows;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterInterface;

trait ParDisplayTrait {

  protected $pagerId = 1;
  protected $numberPerPage = 5;

  /**
   * Overrides the config getter if it doesn't exist.
   */
  protected function config($name) {
    return \Drupal::configFactory()->get($name);
  }

  /**
   * Get renderer service.
   *
   * @return mixed
   */
  public static function getRenderer() {
    return \Drupal::service('renderer');
  }

  /**
   * Render field as a rendered markup field.
   * This prevents the form showing view modes w/ incorrect display weights.
   *
   * @param array $field
   *   The field being rendered.
   *
   * @return mixed
   */
  public function renderMarkupField($field) {
    $rendered_field = $this->getRenderer()->render($field);
    return [
      '#type' => 'markup',
      '#markup' => $rendered_field ? $rendered_field : '<p>(none)</p>',
    ];
  }

  /**
   * Display a FieldItemList that is not an entity reference using the given settings.
   *
   * Adds the required operations to each field item that gets rendered.
   *
   * @param string $section
   *   The section name to display on operation links.
   * @param EntityReferenceFieldItemListInterface $field
   *   The field to render.
   * @param EntityInterface $entity
   *   The entity that we're performing the operations on.
   * @param string $view_mode
   *   The view mode to render the fields from.
   * @param array $operations
   *   The array of operations to add for each field item.
   * @param bool $single
   *   Whether or not to only return the first item.
   *
   * @return null
   */
  public function renderTextField($section, $entity, $field, $view_mode, $operations = [], $single = FALSE) {
    $elements = [];
    foreach ($field as $delta => $value) {
      $elements[$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $field_settings = $this->getParDataManager()->getFieldDisplay($entity, $field, 'default');
      $rendered_field = $value->view($field_settings);

      $elements[$delta]['value'] = $this->renderMarkupField($rendered_field);
      $elements[$delta]['value']['#prefix'] = '<div>';
      $elements[$delta]['value']['#suffix'] = '</div>';

      // Get all of the available entity entity operation links.
      $elements[$delta] += $this->displayEntityOperationLinks($section, $entity, $field, $delta, $operations, $single);
      if ($single) {
        break;
      }
    }

    return $elements;
  }

  /**
   * Display an EntityReferenceFieldItemList using the given settings.
   *
   * Adds the required operations to each field item that gets rendered.
   *
   * @param string $section
   *   The section name to display on operation links.
   * @param EntityReferenceFieldItemListInterface $field
   *   The field to render.
   * @param string $view_mode
   *   The view mode to render the fields from.
   * @param array $operations
   *   The array of operations to add for each field item.
   * @param bool $single
   *   Whether or not to only return the first item.
   *
   * @return array
   */
  public function renderReferenceField($section, $field, $view_mode = 'summary', $operations = [], $single = FALSE) {
    $elements = [];
    foreach ($field->referencedEntities() as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $rendered_entity = $entity_view_builder->view($entity, $view_mode);
      $elements[$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $elements[$delta]['entity'] = $this->renderMarkupField($rendered_entity);

      // Get all of the available entity entity operation links.
      $elements[$delta] += $this->displayEntityOperationLinks($section, $entity, $field, $delta, $operations, $single);
      if ($single) {
        break;
      }
    }

    return $elements;
  }

  /**
   * Display an EntityReferenceFieldItemList using the given settings.
   *
   * Adds the required operations to each field item that gets rendered.
   *
   * @param string $section
   *   The section name to display on operation links.
   * @param EntityInterface[] $entities
   *   The entities to render.
   * @param string $view_mode
   *   The view mode to render the fields from.
   * @param array $operations
   *   The array of operations to add for each field item.
   * @param bool $single
   *   Whether or not to only return the first item.
   *
   * @return array
   */
  public function renderEntities($section, $entities, $view_mode = 'summary', $operations = [], $single = FALSE) {
    $elements = [
      '#type' => 'fieldset',
      '#title' => t("$section"),
    ];
    foreach ($entities as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $rendered_entity = $entity_view_builder->view($entity, $view_mode);
      $elements[$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $elements[$delta]['entity'] = $this->renderMarkupField($rendered_entity);

      if ($single) {
        break;
      }
    }

    return $elements;
  }

  /**
   * @param string $section
   *   The section name to display on operation links.
   * @param EntityInterface $entity
   *   The entity that we're performing the operations on.
   * @param FieldItemListInterface $field
   *   The field that the operation relates to.
   * @param integer $delta
   *   The field delta to display the opperation link for.
   * @param array $operations
   *   An array of operations that we want to get.
   * @param bool $single
   *   Whether or not to only return the first item.
   *
   * @return array
   */
  public function displayEntityOperationLinks($section, $entity, $field, $delta = NULL, $operations = [], $single = FALSE) {
    $operation_links = [];

    // Reference fields need to be rendered slightly differently.
    if ($field instanceof EntityReferenceFieldItemListInterface && !$single) {
      $link_name_suffix = $entity->label();
    }
    else {
      $link_name_suffix = strtolower($field->getFieldDefinition()->getLabel());
    }

    // Only add the edit link if it is in the allowed operations.
    if (isset($operations) && (in_array('edit-entity', $operations) || in_array('edit-field', $operations))) {
      // Depending on whether we're editing the field, or the entity, or both
      // we need to add the delta or entity id to the route params.
      $params = [];
      if (in_array('edit-field', $operations)) {
        $params[$field->getName() . '_delta'] = $delta;
      }
      if (in_array('edit-entity', $operations)) {
        $params[$entity->getEntityTypeId()] = $entity->id();
      }

      try {
        $edit_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('edit_' . $field->getName(), $params)->setText("edit {$link_name_suffix}")->toString();
      }
      catch (ParFlowException $e) {
        $this->getLogger($this->getLoggerChannel())->notice($e);
      }
      if (isset($edit_link)) {
        $operation_links['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', ['@link' => $edit_link]),
        ];
      }
    }

    // @TODO We will eventually need to add delete/revoke/archive and various other operations.
    return ['operations' => $operation_links];
  }

  /**
   * Helper function for rendering field sections.
   *
   * Display all of the fields on the given legal entity
   * with the relevant operational links.
   *
   * @param string $section
   *   The section title to use for this field-set.
   *
   * @param EntityInterface $entity
   *   The entity containing the fields to be rendered.
   *
   * @param array $fields
   *   An array containing the field names and display modes to be rendered.
   *
   * @param array $operations
   *   An array required operations.
   *
   * @param Boolean $title
   *   Weather or not to display a title for this section.
   *
   * @param Boolean $single
   *
   * @param Boolean $section_title_only
   *    An option to only output a title with the correct markup.
   *
   * @return mixed
   */
  public function renderSection($section, $entity, $fields = [], $operations = [], $title = TRUE, $single = FALSE, $section_title_only = FALSE) {

    // If rendering logic is called on an NULL object prevent system failures.
    if (empty($entity)) {
      return;
    }

    $element = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    if ($title) {
      $element['#title'] = t("$section");
    }

    // If we are only we only want a section title return the form elements.
    if ($section_title_only == TRUE) {
      return $element;
    }

    foreach ($fields as $field_name => $view_mode) {
      $rows = [];

      $element[$field_name] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $field = $entity->get($field_name);
      if (!$field->isEmpty()) {
        // Respect renderSection $single param if only to show one record.
        $single_item = $single;
        // If there is only one value treat the field as single.
        if ($field->count() <= 1) {
          $single_item = TRUE;
        }

        // Reference fields need to be rendered slightly differently.
        if ($field instanceof EntityReferenceFieldItemListInterface) {
          $rows = $this->renderReferenceField($section, $field, $view_mode, $operations, $single_item);
        }
        else {
          $rows = $this->renderTextField($section, $entity, $field, $view_mode, $operations, $single_item);
        }

        // Render the rows using a tabulated pager.
        $element[$field_name] = $this->renderTable($rows);
      }
      else {
        $element[$field_name]['items'] = [
          '#type' => 'markup',
          '#markup' => '<p>(none)</p>',
        ];
        // If displaying the add action don't display the edit as well.
        if (!in_array('add', $operations)) {
          $element[$field_name]['items'] += $this->displayEntityOperationLinks($section, $entity, $field, 0, $operations);
        }
      }

      $element[$field_name]['operations'] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      // Only add the add link if it is in the allowed operations.
      $link_name_suffix = strtolower($field->getFieldDefinition()->getLabel());
      if (isset($operations) && (in_array('add', $operations)) && !($single && !$field->isEmpty())) {
        try {
          $add_link = $this->getFlowNegotiator()->getFlow()->getLinkByCurrentOperation('add_' . $field->getName())->setText("add another {$link_name_suffix}")->toString();
        } catch (ParFlowException $e) {
          $this->getLogger($this->getLoggerChannel())->notice($e);
        }
        if (isset($add_link)) {
          $element[$field_name]['operations']['add'] = [
            '#type' => 'markup',
            '#markup' => t('@link', ['@link' => $add_link]),
          ];
        }
      }
    }

    return $element;
  }

  public function renderTable($rows) {
    // Initialize pager and get current page.
    $current_page = pager_default_initialize(count($rows), $this->numberPerPage, $this->pagerId);

    // Split the items up into chunks:
    $chunks = array_chunk($rows, $this->numberPerPage);

    $element = [
      'items' => [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ],
      'pager' => [
        '#type' => 'pager',
        '#theme' => 'pagerer',
        '#element' => $this->pagerId,
        '#weight' => 100,
        '#config' => [
          'preset' => $this->config('pagerer.settings')->get('core_override_preset'),
        ],
      ]
    ];

    // Add the items for our current page to the fieldset.
    foreach ($chunks[$current_page] as $delta => $item) {
      $element[$delta] = $item;
    }

    // Increment the pager ID so that any other pager in this page uses a unique element id.
    $this->pagerId++;

    return $element;
  }

  /**
   * Render completion percentages as a tick.
   *
   * @param $percentage
   *   A percentage.
   *
   * @return mixed
   *   A tick e.g. ✔ or XXX%.
   */
  public function renderPercentageTick($percentage = 0) {

    // @todo decide if this percentage should show at all.
    if ($percentage !== 100) {
      return $percentage . '%';
    }

    // show a UTF-8 ✔.
    return '✔';

  }
}
