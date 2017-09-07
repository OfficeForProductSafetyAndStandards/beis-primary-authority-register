<?php

namespace Drupal\par_dashboards\Controller;

use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParDashboardsDashboardController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if (in_array('par_authority', $roles)) {
      // Need to get the authoirty the user belongs to.
      $build['intro'] = [
        '#type' => 'markup',
        '#markup' => $this->t('Authority name goes here.'),
      ];
    }

    // Need to see what permissions the user has so we can display the correct
    // links.
    // bypass partnership journey,
    // authority partnership journey,
    // business partnership journey,
    // coordinator partnership journey
    // Also need to see if there are any other links based on partnerships to
    // be displayed.

    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($current_user->hasPermission('bypass partnership journey')) {
      $build['partnerships'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Your partnerships'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $build['partnerships']['see'] = [
        '#type' => 'markup',
        '#markup' => t('<a href="/partnerships">See all partnerships</a><br>'),
      ];

      $build['partnerships']['add'] = [
        '#type' => 'markup',
        '#markup' => t('<a href="/partnerships">Create a new partnership (need link)</a>'),
      ];
    }

    $build['partnerships_find'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Find a partnership'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['enforcement'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enforcement notices'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    return parent::build($build);

  }

}
