<?php

namespace Drupal\par_dashboards\Controller;

use Drupal\par_flows\Controller\ParBaseController;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParDashboardsDashboardController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $account = User::load(\Drupal::currentUser()->id());
    $build = [];

    if ($account->hasPermission('manage my authorities')) {
      $this->buildAuthority($build, $account);
    }

    if ($account->hasPermission('manage my organisations')) {
      $this->buildOrganisation($build, $account);
    }

    return parent::build($build);
  }

  /**
   * Build the page for an authority.
   *
   * @param array $build
   *   Referenced array with the current build structure.
   * @param \Drupal\user\Entity\User $account
   *   Details of the current user.
   */
  private function buildAuthority(array &$build, User $account) {
    // Need to get the authority the user belongs to.
    $par_data_manager = \Drupal::service('par_data.manager');
    $memberships = $par_data_manager->hasMemberships($account, TRUE);

    $build['details_intro'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Primary Authority Partner:'),
    ];

    if (!empty($memberships['par_data_authority'])) {
      $par_data_authority = current($memberships['par_data_authority']);

      $authority_builder = $this->getParDataManager()
        ->getViewBuilder('par_data_authority');

      $authority_name = $authority_builder->view($par_data_authority, 'title');
      $authority_name['#prefix'] = '<h1>';
      $authority_name['#suffix'] = '</h1>';

      $build['authority_name'] = $this->renderMarkupField($authority_name);
    }
    else {
      $build['authority_name'] = [
        '#type' => 'markup',
        '#markup' => $this->t('(none)'),
      ];
    }

    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Your partnerships'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['partnerships']['see'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<a href="/partnerships">See all partnerships</a><br>'),
    ];

    $build['partnerships']['add'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<a href="/partnerships">Create a new partnership (TBC:need link)</a>'),
    ];

    $build['partnerships_find'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Find a partnership'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['partnerships_find']['link'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<a href="/partnerships/search">Search for a partnership</a>'),
    ];

    $build['enforcement'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enforcement notices'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
  }

  /**
   * Build the page for an organisation.
   *
   * @param array $build
   *   Referenced array with the current build structure.
   * @param \Drupal\user\Entity\User $account
   *   Details of the current user.
   */
  private function buildOrganisation(array &$build, User $account) {
    $par_data_manager = \Drupal::service('par_data.manager');
    $memberships = $par_data_manager->hasMemberships($account, TRUE);

    $build['details_intro'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Primary Authority Organisation:'),
    ];

    if (!empty($memberships['par_data_organisation'])) {
      $par_data_organisation = current($memberships['par_data_organisation']);

      $organisation_builder = $this->getParDataManager()
        ->getViewBuilder('par_data_organisation');

      $organisation_name = $organisation_builder->view($par_data_organisation, 'name');
      $organisation_name['#prefix'] = '<h1>';
      $organisation_name['#suffix'] = '</h1>';

      $build['business_name'] = $this->renderMarkupField($organisation_name);
    }

    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Your partnerships'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['partnerships']['see'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<a href="/partnerships">See all partnerships</a><br>'),
    ];

    $build['applications'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Applications'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // @todo update this to become a flow link.
    $build['applications']['see'] = [
      '#type' => 'markup',
      '#markup' => $this->t('<a href="/partnerships">See my pending partnerships (TBC:need link)</a><br>'),
    ];

  }

}
