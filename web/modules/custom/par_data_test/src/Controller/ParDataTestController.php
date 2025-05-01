<?php

namespace Drupal\par_data_test\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;

/**
* A controller for resetting par data test content.
*/
class ParDataTestController extends ControllerBase {

  /**
  * The main index page for the styleguide.
  */
  public function reset(): array {
    // Load the install file to reset the data.
    Drupal::moduleHandler()->loadInclude('par_data_test', 'install');

    par_data_test_uninstall();
    par_data_test_install();

    return [
      '#type' => 'markup',
      '#markup' => 'The test data has been reset.'
    ];
  }

}
