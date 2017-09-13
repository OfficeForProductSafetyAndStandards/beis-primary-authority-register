<?php

namespace Drupal\par_flows;

use Drupal\Core\Field\EntityReferenceFieldItemListInterface;

trait ParDisplayTrait {

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
   *
   * @return mixed
   */
  public function renderMarkupField($field) {
    $rendered_field = $this->getRenderer()->render($field);
    return [
      '#type' => 'markup',
      '#markup' => $rendered_field ? $rendered_field : '(none)',
    ];
  }


  public function renderTextField($entity, $field, $view_mode = 'summary', $operations = [], $single = FALSE) {
    foreach ($field->getValue() as $delta => $value) {
      var_dump($value);
    }
    $rendered_field = $entity->{$field->getName()}->view($view_mode);
    $elements = $this->renderMarkupField($rendered_field);

    // We need to get the operations for this.

    return $elements;
  }

  /**
   * @param EntityReferenceFieldItemListInterface $field
   *   The field to render.
   * @param bool $single
   *   Whether or not to only return the first item.
   *
   * @return null
   */
  public function renderReferenceField($field, $view_mode = 'summary', $operations = [], $single = FALSE) {
    $elements = [];
    foreach ($field->referencedEntities() as $delta => $entity) {
      $entity_view_builder = $this->getParDataManager()->getViewBuilder($entity->getEntityTypeId());
      $rendered_entity = $entity_view_builder->view($entity, $view_mode);
      $elements[$delta]['entity'] = $this->renderMarkupField($rendered_entity);

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
          $edit_link = $this->getFlow()->getLinkByCurrentOperation('edit_' . $entity->getEntityTypeId(), $params)->setText('edit')->toString();
        }
        catch (ParFlowException $e) {
          $this->getLogger($this->getLoggerChannel())->error($e);
        }
        if (isset($edit_link)) {
          $elements[$delta]['edit'] = [
            '#type' => 'markup',
            '#markup' => t('@link', ['@link' => $edit_link]),
          ];
        }
      }

      // @TODO We will eventually need to add delete/revoke/archive and various other operations.
    }

    return $elements;
  }

  /**
   * Helper function for rendering field sections.
   *
   * Display all of the fields on the given legal entity
   * with the relevant operational links.
   */
  public function renderSection($section, $entity, $fields, $operations, $title = TRUE, $single = FALSE) {
    $element = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    if ($title) {
      $element['#title'] = t("$section:");
    }

    foreach ($fields as $field_name => $view_mode) {
      $field = $entity->get($field_name);
      if (!$field->isEmpty()) {
        $element['items'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        // Reference fields need to be rendered slightly differently.
        if ($field instanceof EntityReferenceFieldItemListInterface) {
          $element['items'] = $this->renderReferenceField($field, $view_mode, $operations, $single);
        }
        else {
          $element['items'] = $this->renderTextField($entity, $field, $view_mode, $operations, $single);
        }

      }
      else {
        $element['items'] = [
          '#type' => 'markup',
          '#markup' => $this->t('(none)'),
        ];
      }
    }

    $element['operations'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $element['operations']['add'] = [
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('add_legal')->setText('add another')->toString(),
      ]),
    ];

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
