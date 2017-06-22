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

    $elements = [
      '#type' => 'markup',
      '#markup' => t('Will build to render a template shortly.'),
    ];

    $build = [
      '#type' => 'par_styleguide',
      '#children' => $elements,
    ];

    return $build;
  }

}