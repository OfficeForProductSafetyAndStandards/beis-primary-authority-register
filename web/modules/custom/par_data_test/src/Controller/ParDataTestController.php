<?php

namespace Drupal\par_data_test\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* A controller for resetting par data test content.
*/
class ParDataTestController extends ControllerBase {

  protected function getContentManager() {
    return \Drupal::service('par_data_test.manager');
  }

  /**
  * The main index page for the styleguide.
  */
  public function reset() {
    $this->getContentManager()->removeData();
    $this->getContentManager()->importData();

    $build = [
      '#type' => 'markup',
      '#markup' => 'The test data has been reset.'
    ];

    return $build;
  }
}
