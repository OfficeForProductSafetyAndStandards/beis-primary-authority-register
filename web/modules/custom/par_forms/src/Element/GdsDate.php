<?php

namespace Drupal\par_forms\Element;

use Drupal\Core\Render\Element\CompositeFormElementTrait;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a GDS Date element.
 *
 * @FormElement("gds_date")
 */
class GdsDate extends FormElement {

  use CompositeFormElementTrait;

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);

    return [
      '#input' => TRUE,
      '#theme' => 'gds_date',
      '#process' => [[$class, 'processGdsDate']],
      '#pre_render' => [
        [$class, 'preRenderCompositeFormElement'],
        [$class, 'preRenderGdsDate'],
      ],
    ];
  }

  /**
   * Prepare the render array for the template.
   */
  public static function preRenderGdsDate($element) {
    $element['day'] = [
      '#type' => 'textfield',
      '#title' => 'Day',
      '#attributes' => [
        'name' => $element['#name'] ."_day",
        'pattern' => "[0-9]*",
        'size' => 6,
        'class' => ['gds-date-sub-element']
      ],
      '#required' => $element['#required'],
    ];

    $element['month'] = [
      '#type' => 'textfield',
      '#title' => 'Month',
      '#attributes' => [
        'name' => $element['#name'] ."_month",
        'pattern' => "[0-9]*",
        'size' => 6,
        'class' => ['gds-date-sub-element']
      ],
      '#required' => $element['#required'],
    ];

    $element['year'] = [
      '#type' => 'textfield',
      '#title' => 'Year',
      '#attributes' => [
        'name' => $element['#name'] ."_year",
        'pattern' => "[0-9]*",
        'size' => 12,
        'class' => ['gds-date-sub-element']
      ],
      '#required' => $element['#required'],
    ];

    // Default value.
    $element['#default_value'] = ['day', 'month', 'year'];

    return $element;
  }

  /**
   * Copy the user inputs to the parent field value.
   */
  public static function processGdsDate(&$element, FormStateInterface $form_state, &$complete_form) {
    // Get all form inputs
    $inputs = $form_state->getUserInput();

    // Get the parent field name
    $name = $element['#name'];

    // Prep the value.
    if (isset($inputs["{$name}_day"]) && isset($inputs["{$name}_month"]) && isset($inputs["{$name}_year"])) {
      $element['#value'] = ['day' => $inputs["{$name}_day"], 'month' => $inputs["{$name}_month"], 'year' => $inputs["{$name}_year"]];
      $form_state->setValue($name, $element['#value']);
    }

    return $element;
  }
}
