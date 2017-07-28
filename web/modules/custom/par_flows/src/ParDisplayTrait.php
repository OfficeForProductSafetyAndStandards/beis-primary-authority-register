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
   * @return array
   */
  public function renderMarkupField($field) {

    return [
      '#type' => 'markup',
      '#markup' => $this->getRenderer()->render($field)
    ];

  }

}
