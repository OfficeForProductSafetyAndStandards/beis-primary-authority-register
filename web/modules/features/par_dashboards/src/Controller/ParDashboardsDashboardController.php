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
    $this->setCurrentUser();
    $build = [];

    // Your partnerships.
    $partnerships =  $this->getParDataManager()->hasMembershipsByType($this->getUserAccount(), 'par_data_partnership');
    $can_manage_partnerships = $this->getUserAccount()->hasPermission('manage my organisations') || $this->getUserAccount()->hasPermission('manage my authorities');
    $can_create_partnerships = $this->getUserAccount()->hasPermission('apply for partnership');
    if (($partnerships && $can_manage_partnerships) || $can_create_partnerships) {
      $build['partnerships'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Your partnerships'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      // Manage my partnerships link.
      if ($partnerships) {
        $manage_my_partnerships = $this->getLinkByRoute('view.par_user_partnerships.partnerships_page');
        $build['partnerships']['see'] = [
          '#type' => 'markup',
          '#markup' => $manage_my_partnerships->setText('See your partnerships')->toString(),
        ];
      }

      // Create partnerships link.
      if ($can_create_partnerships) {
        $create_partnerships = $this->getLinkByRoute('view.par_user_partnerships.partnerships_page');
        $build['partnerships']['add'] = [
          '#type' => 'markup',
          '#markup' => $create_partnerships->setText('Create a new partnership (TBC:need link)')->toString(),
        ];
      }
    }

    // Applications, partnerships that need completion.
    // @TODO NEED A WAY TO GET INCOMPLETE APLICATIONS (WE DO NOT RECORD THIS YET). NOT MVP.
    if ($this->getUserAccount()->hasPermission('complete partnership organisation details')) {
      $build['applications'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Applications'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
      $manage_my_partnerships = $this->getLinkByRoute('view.par_user_partnerships.partnerships_page');
      $build['applications']['see'] = [
        '#type' => 'markup',
        '#markup' => $manage_my_partnerships->setText('See my pending partnerships (TBC:need link)')->toString(),
      ];
    }

    // Partnerships search link.
    if ($this->getUserAccount()->hasPermission('enforce organisation')) {
      $build['partnerships_find'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Find a partnership'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $search_partnerships = $this->getLinkByRoute('view.partnership_search.enforcment_flow_search_partnerships');
      $build['partnerships_find']['link'] = [
        '#type' => 'markup',
        '#markup' => $search_partnerships->setText('Search for a partnership')->toString(),
      ];
    }

    // Enforcement notices that need attention.
    if ($this->getUserAccount()->hasPermission('enforce organisation')) {
      $build['messages'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Messages'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      // @TODO TO BE ADDED. WE NEED A SEPARATE VIEW PAGE FOR MANAGING ALL NOTICES.
      $search_partnerships = $this->getLinkByRoute('view.par_user_enforcements.enforcment_notice_messages');
      $build['messages']['link'] = [
        '#type' => 'markup',
        '#markup' => $search_partnerships ? $search_partnerships->setText('See all outstanding enforcement notices')->toString() : '(none)',
      ];
    }

    return parent::build($build);
  }

}
