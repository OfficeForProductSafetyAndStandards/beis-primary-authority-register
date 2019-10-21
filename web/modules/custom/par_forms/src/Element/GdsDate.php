<?php

namespace Drupal\par_forms\Element;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Element\CompositeFormElementTrait;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a GDS Date element.
 *
 * Properties:
 * - #default_value: An array with the keys: 'year', 'month', and 'day'.
 *   Defaults to the current date if no value is supplied.
 *
 * @code
 * $form['expiration'] = array(
 *   '#type' => 'date',
 *   '#title' => $this->t('Content expiration'),
 *   '#default_value' => ['year' => 2020, 'month' => 2, 'day' => 15,]
 * );
 * @endcode
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
      '#process' => [
        [$class, 'processGdsDate']
      ],
      '#pre_render' => [
        [$class, 'preRenderCompositeFormElement'],
      ],
      '#date_date_format' => 'Y-m-d',
    ];
  }

  /**
   * Copy the user inputs to the parent field value.
   */
  public static function processGdsDate(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = is_array($element['#value']) ? $element['#value'] : [];

    $day = isset($element['#default_value']['day']) ? $element['#default_value']['day'] : NULL;
    $month = isset($element['#default_value']['month']) ? $element['#default_value']['month'] : NULL;
    $year = isset($element['#default_value']['year']) ? $element['#default_value']['year'] : NULL;

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
      '#default_value' => isset($value['day']) ? $value['day'] : $day,
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
      '#default_value' => isset($value['month']) ? $value['month'] : $month,
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
      '#default_value' => isset($value['year']) ? $value['year'] : $year,
    ];

    // Prep the value.
    $inputs = $form_state->getUserInput();
    $name = $element['#name'];
    if (isset($inputs["{$name}_day"]) && isset($inputs["{$name}_month"]) && isset($inputs["{$name}_year"])) {
      $date_input = implode('-', [$inputs["{$name}_year"], $inputs["{$name}_month"], $inputs["{$name}_day"]]);
      $date_format = !empty($element['#date_date_format']) ? $element['#date_date_format'] : 'Y-m-d';
      $element['#value'] = '';

      try {
        $date = DrupalDateTime::createFromFormat($date_format, $date_input, NULL, ['validate_format' => FALSE]);
        $element['#value'] = $date->format($date_format);
      }
      catch (\Exception $e) {
        $date = NULL;
      }

      $form_state->setValue($name, $element['#value']);
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $date_format = !empty($element['#date_date_format']) ? $element['#date_date_format'] : 'Y-m-d';

    if (is_string($element['#default_value'])) {
      try {
        $date = DrupalDateTime::createFromFormat($date_format, $element['#default_value'], NULL, ['validate_format' => FALSE]);
        $value = [
          'day' => $date->format('d'),
          'month' => $date->format('m'),
          'year' => $date->format('Y'),
        ];
      } catch (\Exception $e) {
        $value = [];
      }
      return $value;
    }
    else {
      return [];
    }
  }
}
