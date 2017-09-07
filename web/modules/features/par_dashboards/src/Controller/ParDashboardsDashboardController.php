<?php

namespace Drupal\par_dashboards\Controller;

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
    $current_user = \Drupal::currentUser();

    if ($current_user->hasPermission('manage my authorities')) {
      // Need to get the authority the user belongs to.
      $par_data_manager = \Drupal::service('par_data.manager');
      $account = User::load($current_user->id());
      $memberships = $par_data_manager->hasMemberships($account, TRUE);

      $build['details_intro'] = [
        '#type' => 'markup',
        '#markup' => t('Primary Authority Partner:'),
      ];

      if (!empty($memberships['par_data_authority'])) {
        $par_data_authority = current($memberships['par_data_authority']);

        $organisation_builder = $this->getParDataManager()
          ->getViewBuilder('par_data_organisation');
        $authority_name = $organisation_builder->view($par_data_authority, 'title');
        $authority_name['#prefix'] = '<h1>';
        $authority_name['#suffix'] = '</h1>';
        $build['authority_name'] = $this->renderMarkupField($authority_name);
      }
      else {
        $build['authority_name'] = [
          '#type' => 'markup',
          '#markup' => t('(none)'),
        ];
      }
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

//    if ($current_user->hasPermission('bypass partnership journey')) {
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
//    }

    $build['partnerships_find'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Find a partnership'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['partnerships_find']['link'] = [
      '#type' => 'markup',
      '#markup' => t('<a href="/partnerships/search">Search for a partnership</a>'),
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
