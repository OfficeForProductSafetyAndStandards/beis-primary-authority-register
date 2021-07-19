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
    $entity_type_manager = $container->get('entity_type.manager');
    return new static(
      $entity_type_manager->getStorage('par_flow'),
      $container->get('par_data.manager'),
      $container->get('current_user'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Helpdesk | Dashboard';
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = []) {
    // The dashboard is too complex and too important a page to cache.
    $this->killSwitch->trigger();
    $build = [];

    // Manage partnerships.
    $build['statistics'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']],
    ];
    $build['statistics']['active_partnerships'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['statistics']['covered_organisations'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['statistics']['active_users'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['active_users']],
      '#create_placeholder' => TRUE,
    ];
    $statistics_link = $this->getLinkByRoute('par_reporting.reports_page');
    if ($statistics_link) {
      $build['statistics']['view_all'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#attributes' => ['class' => 'column-full'],
        '#value' => !empty($statistics_link) ? $statistics_link->setText('View all statistics')->toString() : '',
      ];
    }

    $manage_partnerships = $this->getLinkByRoute('view.helpdesk_dashboard.par_rd_helpdesk_dashboard_page');
    $manage_link = $manage_partnerships->setText('Manage partnerships')->toString();
    $build['partnerships']['manage'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_link}</p>",
    ];

    // Manage partnerships.
    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Partnerships'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']]
    ];

    $manage_partnerships = $this->getLinkByRoute('view.helpdesk_dashboard.par_rd_helpdesk_dashboard_page');
    $manage_link = $manage_partnerships->setText('Manage partnerships')->toString();
    $build['partnerships']['manage'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_link}</p>",
    ];

    $partnership_report = $this->getLinkByRoute('view.helpdesk_dashboard.helpdesk_csv');
    $partnership_report_link = $partnership_report->setText('Download CSV partnership report')->toString();
    $build['partnerships']['report'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$partnership_report_link}</p>",
    ];

    // Partnerships search link.
    $search_partnerships = $this->getLinkByRoute('view.partnership_search.enforcment_flow_search_partnerships');
    $search_link = $search_partnerships->setText('Search for a partnership')->toString();
    $build['partnerships']['link'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$search_link}</p>",
      '#pre' => "<p>Search for active partnerships to check advice and raise notice of enforcement action.</p>",
    ];


    // Manage authorities and organisations.
    $build['institutions'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Authorities & Organisations'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $manage_authorities_link = $this->getLinkByRoute('view.helpdesk_authorities.par_helpdesk_authority_page');
    if ($manage_authorities_link) {
      $build['institutions']['authorities'] = [
        '#type' => 'markup',
        '#markup' => "<p>{$manage_authorities_link->setText('Manage authorities')->toString()}</p>",
      ];
    }
    $manage_organisations_link = $this->getLinkByRoute('view.helpdesk_organisations.par_helpdesk_organisation_page');
    if ($manage_organisations_link) {
      $build['institutions']['organisations'] = [
        '#type' => 'markup',
        '#markup' => "<p>{$manage_organisations_link->setText('Manage organisations')->toString()}</p>",
      ];
    }



    // Manage users.
    $build['people'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']]
    ];

    $manage_users = $this->getLinkByRoute('view.par_people.people');
    $manage_users_link = $manage_users->setText('Manage people')->toString();
    $build['people']['people'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$manage_users_link}</p>",
    ];

    $manage_subscriptions = $this->getLinkByRoute('view.subscriptions.subscription_list');
    if ($manage_subscriptions) {
      $manage_subscriptions_link = $manage_subscriptions->setText('Manage subscriptions')->toString();
      $build['people']['subscriptions'] = [
        '#type' => 'markup',
        '#markup' => "<p>{$manage_subscriptions_link}</p>",
      ];
    }

    // Manage enforcements.
    $build['enforcements'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enforcements'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']]
    ];
    $link = $this->getLinkByRoute('view.par_user_enforcements.enforcement_notices_page')
      ->setText('Manage enforcement notices')
      ->toString();
    $build['enforcements'][] = [
      '#type' => 'markup',
      '#markup' => "<p>{$link}</p>",
    ];

    $deviation_requests_link = $this->getLinkByRoute('view.par_user_deviation_requests.deviation_requests_page')
      ->setText('Manage deviation requests')
      ->toString();
    $build['enforcements'][] = [
      '#type' => 'markup',
      '#markup' => "<p>{$deviation_requests_link}</p>",
    ];

    $inspection_feedback_link = $this->getLinkByRoute('view.par_user_inspection_feedback.inspection_feedback_page')
      ->setText('Manage inspection feedback')
      ->toString();
    $build['enforcements'][] = [
      '#type' => 'markup',
      '#markup' => "<p>{$inspection_feedback_link}</p>",
    ];

    // Manage enquiries.
    $build['enquiries'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enquiries'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#cache' => ['contexts' => ['user.par_memberships:authority']]
    ];

    $general_enquiries_link = $this->getLinkByRoute('view.par_user_general_enquiries.general_enquiries_page')
      ->setText('Manage general enquiries')
      ->toString();
    $build['enquiries'][] = [
      '#type' => 'markup',
      '#markup' => "<p>{$general_enquiries_link}</p>",
    ];

    return $build;
  }

}
