<?php

namespace Drupal\par_invite\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * A controller for all styleguide page output.
 */
class ParInviteUnsuccessfulController extends ControllerBase {

  /**
   * The main index page for the styleguide.
   */
  public function content() {

    $build['intro'] = [
      '#markup' => t('<p>We are sorry but the token provided is no longer valid.</p>'),
    ];

    return $build;
  }

}
