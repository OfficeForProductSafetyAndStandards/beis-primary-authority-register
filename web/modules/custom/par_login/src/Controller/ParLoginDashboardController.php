<?php

namespace Drupal\par_login\Controller;

use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParLoginDashboardController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  public function content() {

    $build['intro'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Intro to new dashboard'),
    ];

    return parent::build($build);

  }

}
