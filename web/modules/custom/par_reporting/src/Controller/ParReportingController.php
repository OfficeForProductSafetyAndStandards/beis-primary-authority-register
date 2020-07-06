<?php

namespace Drupal\par_reporting\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
* A controller managing par reporting pages.
*/
class ParReportingController extends ControllerBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $parDataManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entity_type_manager) {
    $this->parDataManager = $par_data_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_data.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
  * The main page for listing par entities.
  */
  public function general() {
    $build = [];

    // Statistics related to partnerships.
    $build['partnerships'] = [
      '#type' => 'fieldset',
      '#title' => 'Partnerships',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['partnerships']['active'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnerships']['pending'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_pending_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnerships']['revoked'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_revoked_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnerships']['direct'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_direct_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnerships']['coordinated'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_coordinated_partnerships']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to partnership documents.
    $build['documents'] = [
      '#type' => 'fieldset',
      '#title' => 'Partnership documents',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['documents']['inspection_plans'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['active_partnerships_with_inspection_plans']],
      '#create_placeholder' => TRUE,
    ];
    $build['documents']['advice'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['active_advice']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to organisations and legal entities.
    $build['covered_organisations'] = [
      '#type' => 'fieldset',
      '#title' => 'Organisations',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['covered_organisations']['total'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['covered_organisations']['direct'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_direct_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['covered_organisations']['coordinated'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_coordinated_members']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to organisations and legal entities.
    $build['authorities'] = [
      '#type' => 'fieldset',
      '#title' => 'Authorities',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['authorities']['total'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_authorities']],
      '#create_placeholder' => TRUE,
    ];
    $build['authorities']['covering_partnerships'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_primary_authorities']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to organisations and legal entities.
    $build['messages'] = [
      '#type' => 'fieldset',
      '#title' => 'Notifications',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['messages']['enforcements'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_enforcement_notices']],
      '#create_placeholder' => TRUE,
    ];
    $build['messages']['enquiries'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_enquiries']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to users.
    $build['users'] = [
      '#type' => 'fieldset',
      '#title' => 'Users',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['users']['total'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_users']],
      '#create_placeholder' => TRUE,
    ];
    $build['users']['active'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['active_users']],
      '#create_placeholder' => TRUE,
    ];
    $build['users']['recent'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['recent_users']],
      '#create_placeholder' => TRUE,
    ];

    return $build;
  }

}
