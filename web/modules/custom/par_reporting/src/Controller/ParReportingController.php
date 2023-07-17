<?php

namespace Drupal\par_reporting\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
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
   * @var \Drupal\par_data\ParDataManagerInterface
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
    $build['partnership_applications'] = [
      '#type' => 'container',
      '#title' => 'Partnership Applications',
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
    ];
    $build['partnership_applications']['active'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnership_applications']['pending'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_pending_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnership_applications']['revoked'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_revoked_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnership_applications']['direct'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_direct_partnerships']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnership_applications']['coordinated'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_active_coordinated_partnerships']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to partnerships.
    $build['partnerships'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => t('Partnerships'),
      ],
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
    ];
    $build['partnerships']['total'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnerships']['direct'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_direct_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['partnerships']['coordinated'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_coordinated_members']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to organisations and legal entities.
    $build['businesses_in_partnership'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => t('Organisations in a partnership'),
      ],
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
    ];
    $build['businesses_in_partnership']['total'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_unique_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['businesses_in_partnership']['direct'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_unique_direct_businesses']],
      '#create_placeholder' => TRUE,
    ];
    $build['businesses_in_partnership']['coordinated'] = [
      '#lazy_builder' => ['par_reporting.manager:render', ['total_unique_coordinated_members']],
      '#create_placeholder' => TRUE,
    ];

    // Statistics related to partnership documents.
    $build['documents'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => t('Partnership documents'),
      ],
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
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
    $build['authorities'] = [
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => t('Authorities'),
      ],
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
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
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => t('Notifications'),
      ],
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
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
      '#type' => 'container',
      'heading' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#attributes' => ['class' => ['govuk-heading-m']],
        '#value' => $this->t('Users'),
      ],
      '#attributes' => ['class' => ['govuk-grid-row', 'form-group']],
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
