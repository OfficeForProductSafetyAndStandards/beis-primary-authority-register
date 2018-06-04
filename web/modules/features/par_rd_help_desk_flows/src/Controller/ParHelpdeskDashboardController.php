<?php

namespace Drupal\par_rd_help_desk_flows\Controller;

use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_rd_help_desk_flows\ParFlowAccessTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller for the PAR Helpdesk dashboard.
 */
class ParHelpdeskDashboardController extends ControllerBase {

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
   * {@inheritdoc}
   */
  public function build($build = []) {
    // The dashboard is too complex and too important a page to cache.
    $this->killSwitch->trigger();
    $build = [];

    // Manage partnerships.
    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Manage partnerships'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']]
    ];

    $manage_partnerships = $this->getLinkByRoute('view.helpdesk_dashboard.par_rd_helpdesk_dashboard_page');
    $manage_link = $manage_partnerships->setText('Manage live partnerships')->toString();
    $build['partnerships']['manage'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_link}</p>",
    ];

    $partnership_report = $this->getLinkByRoute('view.par_user_partnerships.par_user_partnership_applications');
    $partnership_report_link = $partnership_report->setText('Download CSV partnership report')->toString();
    $build['partnerships']['report'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$partnership_report_link}</p>",
    ];


    // Manage users.
    $build['users'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Manage people'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']]
    ];

    $manage_users = $this->getLinkByRoute('view.user_admin_people.helpdesk_users');
    $manage_users_link = $manage_users->setText('Manage user accounts')->toString();
    $build['users']['manage'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_users_link}</p>",
    ];

    return $build;
  }

}
