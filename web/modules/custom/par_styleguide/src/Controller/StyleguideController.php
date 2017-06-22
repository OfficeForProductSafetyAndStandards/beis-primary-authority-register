<?php

namespace Drupal\par_styleguide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* A controller for all styleguide page output.
*/
class StyleguideController extends ControllerBase {

  /**
  * The main index page for the styleguide
  */
  public function index() {

    $build = [
      '#theme' => 'par_styleguide',
    ];

    return $build;
  }

}
