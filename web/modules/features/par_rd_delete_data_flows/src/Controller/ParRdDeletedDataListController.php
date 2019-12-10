<?php

namespace Drupal\par_rd_delete_data_flows\Controller;

use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_rd_help_desk_flows\ParFlowAccessTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller for the PAR Helpdesk dashboard.
 */
class ParRdDeletedDataListController extends ControllerBase {

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
    return 'Helpdesk | Deleted PAR Data';
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = []) {
    // The dashboard is too complex and too important a page to cache.
    $this->killSwitch->trigger();

    $build['entity_types'] = [
      '#type' => 'container',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'info' => [
        '#type' => 'markup',
        '#markup' => $this->t('<p><em>All</em> this data must be removed by the end of 2019. Any data not removed will be automatically cleaned up in the new year. If there is any item that you believe should not be removed please contact the development team and we will investigate, please send the ID indicated in brackets after each item.</p>'),
      ],
      'help' => [
        '#type' => 'markup',
        '#markup' => $this->t('<p>If you want to know more details about a specific piece of data, this can be found after you click on the deletion link.</p>'),
      ],
    ];

    foreach ($this->getParDataManager()->getParEntityTypes() as $entity_type) {
      $deleted_data = $this->getParDataManager()->getEntitiesByProperty($entity_type->id(), 'deleted', 1, FALSE);
      $count = count($deleted_data);

      if ($count > 0) {
        if ($count > $this->numberPerPage) {
          $pager = $this->getUniquePager()->getPager("delete:{$entity_type->id()}");

          // Initialize pager and get current page.
          $current_page = pager_default_initialize($count, $this->numberPerPage, $pager);

          // Split the items up into chunks:
          $chunks = array_chunk($deleted_data, $this->numberPerPage);

          $build['entity_types'][$entity_type->id()] = [
            '#type' => 'fieldset',
            '#title' => t($entity_type->getLowercaseLabel()),
            '#attributes' => ['class' => ['form-group']],
            'items' => [
              '#type' => 'container',
              '#collapsible' => FALSE,
              '#collapsed' => FALSE,
            ],
            'pager' => [
              '#type' => 'pager',
              '#theme' => 'pagerer',
              '#element' => $pager,
              '#weight' => 100,
              '#config' => [
                'preset' => $this->config('pagerer.settings')
                  ->get('core_override_preset'),
              ],
            ]
          ];

          // Add the items for our current page to the fieldset.
          foreach ($chunks[$current_page] as $delta => $entity) {
            try {
              $route_params['entity_type'] = $entity->getEntityTypeId();
              $route_params['entity_id'] = $entity->id();
              $link_options = [
                'absolute' => TRUE,
                'attributes' => ['class' => 'flow-link']
              ];

              $url = Url::fromRoute('par_rd_delete_data_flows.delete_data_confirm', $route_params, $link_options);
              $link = Link::fromTextAndUrl("View & delete {$entity->label()}", $url);
            }
            catch (ParFlowException $e) {
              $this->getLogger($this->getLoggerChannel())->notice($e);
            }

            if (isset($link)) {
              $build['entity_types'][$entity_type->id()]['items'][$delta] = [
                '#type' => 'markup',
                '#markup' => "<p>{$link->toString()} ({$entity->id()})</p>",
              ];
            }
          }
        }
        else {
          $build['entity_types'][$entity_type->id()] = [
            '#type' => 'fieldset',
            '#title' => t($entity_type->getLowercaseLabel()),
            '#attributes' => ['class' => ['form-group']],
            'items' => [
              '#type' => 'container',
              '#collapsible' => FALSE,
              '#collapsed' => FALSE,
            ],
          ];

          // Add the items for our current page to the fieldset.
          foreach ($deleted_data as $delta => $entity) {
            try {
              $route_params['entity_type'] = $entity->getEntityTypeId();
              $route_params['entity_id'] = $entity->id();
              $link_options = [
                'absolute' => TRUE,
                'attributes' => ['class' => 'flow-link']
              ];

              $url = Url::fromRoute('par_rd_delete_data_flows.delete_data_confirm', $route_params, $link_options);
              $link = Link::fromTextAndUrl("View & delete {$entity->label()}", $url);
            }
            catch (ParFlowException $e) {
              $this->getLogger($this->getLoggerChannel())->notice($e);
            }

            if (isset($link)) {
              $build['entity_types'][$entity_type->id()]['items'][$delta] = [
                '#type' => 'markup',
                '#markup' => "<p>{$link->toString()}<span> ({$entity->id()})</span></p>",
              ];
            }
          }
        }

      }
    }

//    // Manage partnerships.
//    $build['partnerships'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('Partnerships'),
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//      '#cache' => ['contexts' => ['user.par_memberships:authority']]
//    ];
//
//    $manage_partnerships = $this->getLinkByRoute('view.helpdesk_dashboard.par_rd_helpdesk_dashboard_page');
//    $manage_link = $manage_partnerships->setText('Manage partnerships')->toString();
//    $build['partnerships']['manage'] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$manage_link}</p>",
//    ];
//
//    $partnership_report = $this->getLinkByRoute('view.helpdesk_dashboard.helpdesk_csv');
//    $partnership_report_link = $partnership_report->setText('Download CSV partnership report')->toString();
//    $build['partnerships']['report'] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$partnership_report_link}</p>",
//    ];
//
//    // Partnerships search link.
//    $search_partnerships = $this->getLinkByRoute('view.partnership_search.enforcment_flow_search_partnerships');
//    $search_link = $search_partnerships->setText('Search for a partnership')->toString();
//    $build['partnerships']['link'] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$search_link}</p>",
//      '#pre' => "<p>Search for active partnerships to check advice and raise notice of enforcement action.</p>",
//    ];
//
//
//    // Manage authorities and organisations.
//    $build['institutions'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('Authorities & Organisations'),
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//    ];
//    $manage_authorities_link = $this->getLinkByRoute('view.helpdesk_authorities.par_helpdesk_authority_page');
//    if ($manage_authorities_link) {
//      $build['institutions']['authorities'] = [
//        '#type' => 'markup',
//        '#markup' => "<p>{$manage_authorities_link->setText('Manage authorities')->toString()}</p>",
//      ];
//    }
//    $manage_organisations_link = $this->getLinkByRoute('view.helpdesk_organisations.par_helpdesk_organisation_page');
//    if ($manage_organisations_link) {
//      $build['institutions']['organisations'] = [
//        '#type' => 'markup',
//        '#markup' => "<p>{$manage_organisations_link->setText('Manage organisations')->toString()}</p>",
//      ];
//    }
//
//
//
//    // Manage users.
//    $build['people'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('People'),
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//      '#cache' => ['contexts' => ['user.par_memberships:authority']]
//    ];
//
////    $manage_users = $this->getLinkByRoute('view.user_admin_people.helpdesk_users');
////    $manage_users_link = $manage_users->setText('Manage user accounts')->toString();
////    $build['people']['users'] = [
////      '#type' => 'markup',
////      '#markup' => "<p>{$manage_users_link}</p>",
////    ];
//
//    $manage_users = $this->getLinkByRoute('view.par_people.people');
//    $manage_users_link = $manage_users->setText('Manage people')->toString();
//    $build['people']['people'] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$manage_users_link}</p>",
//    ];
//
//    // Manage enforcements.
//    $build['enforcements'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('Enforcements'),
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//      '#cache' => ['contexts' => ['user.par_memberships:authority']]
//    ];
//    $link = $this->getLinkByRoute('view.par_user_enforcements.enforcement_notices_page')
//      ->setText('Manage enforcement notices')
//      ->toString();
//    $build['enforcements'][] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$link}</p>",
//    ];
//
//    $deviation_requests_link = $this->getLinkByRoute('view.par_user_deviation_requests.deviation_requests_page')
//      ->setText('Manage deviation requests')
//      ->toString();
//    $build['enforcements'][] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$deviation_requests_link}</p>",
//    ];
//
//    $inspection_feedback_link = $this->getLinkByRoute('view.par_user_inspection_feedback.inspection_feedback_page')
//      ->setText('Manage inspection feedback')
//      ->toString();
//    $build['enforcements'][] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$inspection_feedback_link}</p>",
//    ];
//
//    // Manage enquiries.
//    $build['enquiries'] = [
//      '#type' => 'fieldset',
//      '#title' => $this->t('Enquiries'),
//      '#attributes' => ['class' => 'form-group'],
//      '#collapsible' => FALSE,
//      '#collapsed' => FALSE,
//      '#cache' => ['contexts' => ['user.par_memberships:authority']]
//    ];
//
//    $general_enquiries_link = $this->getLinkByRoute('view.par_user_general_enquiries.general_enquiries_page')
//      ->setText('Manage general enquiries')
//      ->toString();
//    $build['enquiries'][] = [
//      '#type' => 'markup',
//      '#markup' => "<p>{$general_enquiries_link}</p>",
//    ];

    return $build;
  }

}
