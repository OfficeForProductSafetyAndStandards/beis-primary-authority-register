<?php

namespace Drupal\par_flows;

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
