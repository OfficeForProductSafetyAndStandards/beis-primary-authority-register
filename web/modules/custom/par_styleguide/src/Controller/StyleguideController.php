<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* A controller for all styleguide page output.
*/
class StyleguideController extends ControllerBase {

  /**
  * The main index page for the styleguide.
  */
  public function index() {

    $build = [
      '#theme' => 'par_styleguide',
    ];

    return $build;
  }

  /**
   * get an internal page.
   *
   */
  public function data() {

    $build = [
      '#theme' => 'par_styleguide_data',
    ];

    return $build;

  }

  /**
   * get an internal page.
   *
   */
  public function pagination() {

    $build = [
      '#theme' => 'par_styleguide_pagination',
    ];

    return $build;

  }

}
