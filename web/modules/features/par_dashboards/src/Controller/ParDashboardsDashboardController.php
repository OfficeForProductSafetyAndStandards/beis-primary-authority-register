<?php

namespace Drupal\par_dashboards\Controller;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\user\Entity\User;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParDashboardsDashboardController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  public function content() {

    $build['intro'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Intro to new dashboard'),
    ];

    // Need to see what permissions the user has so we can display the correct
    // links.
    // bypass partnership journey,
    // authority partnership journey,
    // business partnership journey,
    // coordinator partnership journey
    // Also need to see if there are any other links based on partnerships to
    // be displayed.
    $account = User::load(\Drupal::currentUser()->id());

    if ($account->hasPermission('bypass partnership journey')) {
      $build['partnerships'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $build['partnerships']['link'] = [
        '#type' => 'markup',
        '#markup' => t('<a href="/partnerships">Partnership list</a>'),
      ];
    }

    return parent::build($build);

  }

}
