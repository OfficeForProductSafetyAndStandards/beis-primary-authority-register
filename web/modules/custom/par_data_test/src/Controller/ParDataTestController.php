<?php

namespace Drupal\par_data_test\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* A controller for resetting par data test content.
*/
class ParDataTestController extends ControllerBase {

  /**
  * The main index page for the styleguide.
  */
  public function reset() {
    // Load the install file to reset the data.
    module_load_include('install', 'par_data_test');

    par_data_test_uninstall();
    par_data_test_install();

    $build = [
      '#type' => 'markup',
      '#markup' => 'The test data has been reset.'
    ];

    return $build;
  }

}
