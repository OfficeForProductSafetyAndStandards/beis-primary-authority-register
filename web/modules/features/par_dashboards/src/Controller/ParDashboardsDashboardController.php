<?php

namespace Drupal\par_dashboards\Controller;

use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParRedirectTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParDashboardsDashboardController extends ControllerBase {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use ParControllerTrait;

  /**
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The current user object.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user object.
   */
  public function __construct(ConfigEntityStorageInterface $flow_storage, ParDataManagerInterface $par_data_manager, AccountInterface $current_user) {
    $this->flowStorage = $flow_storage;
    $this->parDataManager = $par_data_manager;
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
      $container->get('current_user')
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
  public function content() {
    $build = [];

    // Your partnerships.
    $partnerships =  $this->getParDataManager()->hasMembershipsByType($this->getCurrentUser(), 'par_data_partnership');
    $can_manage_partnerships = $this->getCurrentUser()->hasPermission('manage my organisations') || $this->getCurrentUser()->hasPermission('manage my authorities');
    $can_create_partnerships = $this->getCurrentUser()->hasPermission('apply for partnership');
    if (($partnerships && $can_manage_partnerships) || $can_create_partnerships) {
      // Cache context needs to be added for users with memberships.
      $build['partnerships'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Your partnerships'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#cache' => ['contexts' => ['user.par_memberships:authority']]
      ];

      // Manage my partnerships link.
      if (($partnerships && $can_manage_partnerships)) {
        $manage_my_partnerships = $this->getLinkByRoute('view.par_user_partnerships.partnerships_page');
        $manage_link = $manage_my_partnerships->setText('See your partnerships')->toString();
        $build['partnerships']['see'] = [
          '#type' => 'markup',
          '#markup' => "<p>{$manage_link}</p>",
        ];
      }

      // Create partnerships link.
      if ($can_create_partnerships) {
        $create_partnerships = $this->getLinkByRoute('par_partnership_flows.partnership_application_start');
        $apply_link = $create_partnerships->setText('Apply for a new partnership')->toString();
        $build['partnerships']['add'] = [
          '#type' => 'markup',
          '#markup' => "<p>{$apply_link}</p>",
        ];
      }
    }

    // Partnerships search link.
    if ($this->getCurrentUser()->hasPermission('enforce organisation')) {
      $build['partnerships_find'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Find a partnership'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $search_partnerships = $this->getLinkByRoute('view.partnership_search.enforcment_flow_search_partnerships');
      $search_link = $search_partnerships->setText('Search for a partnership')->toString();

      $build['partnerships_find']['text'] = [
        '#type' => 'markup',
        '#markup' => "<p>Search for active partnerships to check advice and raise notice of enforcement action.</p>",
      ];
      $build['partnerships_find']['link'] = [
        '#type' => 'markup',
        '#markup' => "<p>{$search_link}</p>",
      ];
    }

    // Enforcement Notice links.
    if ($this->getCurrentUser()->hasPermission('enforce organisation')) {
      $build['messages'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Messages'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      if ($this->getCurrentUser()->hasPermission('send enforcement notice')) {
        $link = $this->getLinkByRoute('view.par_user_enforcement_list.enforcement_notices_sent')
          ->setText('See enforcement notifications sent')
          ->toString();

        $build['messages'][] = [
          '#type' => 'markup',
          '#markup' => "<p>{$link}</p>",
        ];
      }

      if ($this->getCurrentUser()->hasPermission('approve enforcement notice')) {
        $link = $this->getLinkByRoute('view.par_user_enforcement_list.enforcement_notices_received')
          ->setText('See enforcement notifications received')
          ->toString();

        $build['messages'][] = [
          '#type' => 'markup',
          '#markup' => "<p>{$link}</p>",
        ];
      }
    }

    return $build;
  }

}
