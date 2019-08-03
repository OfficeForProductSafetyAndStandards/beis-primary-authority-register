<?php

namespace Drupal\par_dashboards\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_dashboards\ParFlowAccessTrait;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParRedirectTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParDashboardsDashboardController extends ControllerBase {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use ParControllerTrait;

  /**
   * The response cache kill switch.
   */
  protected $killSwitch;

  /**
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The current user object.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user object.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The page cache kill switch.
   */
  public function __construct(ConfigEntityStorageInterface $flow_storage, ParDataManagerInterface $par_data_manager, AccountInterface $current_user, KillSwitch $kill_switch) {
    $this->flowStorage = $flow_storage;
    $this->parDataManager = $par_data_manager;
    $this->killSwitch = $kill_switch;
    $this->setCurrentUser($current_user);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_flow'),
      $container->get('par_data.manager'),
      $container->get('current_user'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return $this->getDefaultTitle();
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   *
   * @return AccessResult
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    // Disallow anyone with the permission to view the helpdesk dashboard.
    if ($account->hasPermission('access helpdesk')) {
      // Set an error if this action has already been reviewed.
      return AccessResult::forbidden('Access to the standard dashboard is disabled for helpdesk users.');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function content() {
    // The dashboard is too complex and too important a page to cache.
    $this->killSwitch->trigger();
    $account = $this->getCurrentUser();
    $build = [];

    $build['welcome'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Welcome'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      [
        '#type' => 'markup',
        '#markup' => "<p>Hello! Welcome to the Primary Authority Register.</p>",
      ],
    ];

    $can_manage_partnerships = $this->getCurrentUser()->hasPermission('confirm partnership') ||
      $this->getCurrentUser()->hasPermission('update partnership authority details') ||
      $this->getCurrentUser()->hasPermission('update partnership organisation details');
    $can_create_partnerships = $this->getCurrentUser()->hasPermission('apply for partnership');
    if ($can_manage_partnerships || $can_create_partnerships) {
      $build['partnerships'] = [
        '#lazy_builder' => [
          'par_dashboards.components:managePartnershipComponent',
          [FALSE]
        ],
        '#create_placeholder' => TRUE
      ];
    }

    // Partnerships search link.
    if ($this->getCurrentUser()->hasPermission('search partnerships')) {
      $build['partnerships_find'] = [
        '#lazy_builder' => [
          'par_dashboards.components:searchPartnershipComponent',
          []
        ],
        '#create_placeholder' => TRUE
      ];
    }

    // Notification links.
    if ($this->getCurrentUser()->hasPermission('review deviation request')
      || $this->getCurrentUser()->hasPermission('view deviation request')
      || $this->getCurrentUser()->hasPermission('approve enforcement notice')
      || $this->getCurrentUser()->hasPermission('view enforcement notice')
      || $this->getCurrentUser()->hasPermission('send enforcement notice')
      || $this->getCurrentUser()->hasPermission('view general enquiry')
      || $this->getCurrentUser()->hasPermission('view inspection feedback')) {

      $build['messages'] = [
        '#lazy_builder' => [
          'par_dashboards.components:messagesComponent',
          [FALSE]
        ],
        '#create_placeholder' => TRUE
      ];
    }

//    @TODO Added as part of PAR-1439, but these links should only be enabled
//    when the entire feature is complete.
//
//    // Authority & organisation management links.
//    if ($this->getCurrentUser()->hasPermission('update partnership authority details')
//      || $this->getCurrentUser()->hasPermission('update partnership organisation details')) {
//      $build['institutions'] = [
//        '#lazy_builder' => [
//          'par_dashboards.components:manageInstitutionsComponent',
//          []
//        ],
//        '#create_placeholder' => TRUE
//      ];
//    }

    // User management
    if ($this->getCurrentUser()->hasPermission('manage par profile')) {
      $build['people'] = [
        '#lazy_builder' => [
          'par_dashboards.components:manageUsersComponent',
          []
        ],
        '#create_placeholder' => TRUE
      ];
    }

    // User controls.
    if ($this->getCurrentUser()->hasPermission('manage par profile')) {
      $build['user'] = [
        '#lazy_builder' => [
          'par_dashboards.components:manageProfileComponent',
          []
        ],
        '#create_placeholder' => TRUE
      ];
    }

    return $build;
  }

}
