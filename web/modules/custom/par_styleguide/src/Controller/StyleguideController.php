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

    $sections = [
      'example_section' => [
        'title' => $this->t('Section Title'),
        'description' => $this->t('This is the description text.'),
      ]
    ];

    $build = [
      '#theme' => 'par_styleguide',
      '#sections' => $sections,
    ];

    return $build;
  }

}