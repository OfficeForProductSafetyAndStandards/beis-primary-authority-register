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
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getActivePartnerships(),
      '#label' => 'Active',
    ];
    $build['partnerships']['pending'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getPendingPartnerships(),
      '#label' => 'Pending',
    ];
    $build['partnerships']['revoked'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getRevokedPartnerships(),
      '#label' => 'Revoked',
    ];
    $build['partnerships']['direct'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getActivePartnerships('direct'),
      '#label' => 'Active direct partnerships',
    ];
    $build['partnerships']['coordinated'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getActivePartnerships('coordinated'),
      '#label' => 'Active coordinated partnerships',
    ];

    // Statistics related to organisations and legal entities.
    $build['covered_organisations'] = [
      '#type' => 'fieldset',
      '#title' => 'Organisations',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['covered_organisations']['total'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getCoveredOrganisations(),
      '#label' => 'Covered by a partnership',
    ];
    $build['covered_organisations']['direct'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getCoveredOrganisations('direct'),
      '#label' => 'Covered by a direct partnership',
    ];
    $build['covered_organisations']['coordianted'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getCoveredOrganisations('coordinated'),
      '#label' => 'Covered by a coordinated partnership',
    ];

    // Statistics related to organisations and legal entities.
    $build['authorities'] = [
      '#type' => 'fieldset',
      '#title' => 'Authorities',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['authorities']['total'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getAuthorities(),
      '#label' => 'Local authorities',
    ];
    $build['authorities']['covering_partnerships'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getPrimaryAuthorities(),
      '#label' => 'Primary authorities',
    ];
    $build['authorities']['percentage_covered'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getPrimaryAuthorityPercentage() . '%',
      '#label' => 'Authorities acting as a primary authority',
    ];
    $build['authorities']['authorities_with_users'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getAuthoritiesWithUser(),
      '#label' => 'Authorities with at least one user',
    ];

    // Statistics related to users.
    $build['users'] = [
      '#type' => 'fieldset',
      '#title' => 'Users',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['users']['total'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getUsers(),
      '#label' => 'Total users',
    ];
    $build['users']['active_6'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getActiveUsers(['par_authority', 'par_enforcement', 'par_organisation'], 6),
      '#label' => 'PAR users active in the last six months',
    ];
    $build['users']['active'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getActiveUsers(['par_authority', 'par_enforcement', 'par_organisation']),
      '#label' => 'PAR users active in the last month',
    ];

    // Statistics related to enforcements.
    $build['enforcements'] = [
      '#type' => 'fieldset',
      '#title' => 'Notices of enforcement action',
      '#attributes' => ['class' => ['grid-row', 'form-group']],
    ];
    $build['enforcements']['total'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getEnforcements(),
      '#label' => 'Total enforcements',
    ];
    $build['enforcements']['recent'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getRecentEnforcements(),
      '#label' => 'Enforcements in the last month',
    ];
    $build['enforcements']['average_actions'] = [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getAverageActions(),
      '#label' => 'Average number of actions per enforcement',
    ];

    return $build;
  }

  public function getActivePartnerships($type = NULL) {
    $query = $this->parDataManager->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');

    if ($type) {
      // @TODO There's a bug where our implementation of lowercase postgres matching
      // causes issues with multiple conditions using of the '=' operator.
      $query->condition('partnership_type', $type, 'CONTAINS');
    }

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);


    return $query->count()->execute();
  }

  public function getPendingPartnerships() {
    $query = $this->parDataManager->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd', '<>');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    return $query->count()->execute();
  }

  public function getRevokedPartnerships() {
    $query = $this->parDataManager->getEntityQuery('par_data_partnership')
      ->condition('revoked', 1);

    return $query->count()->execute();
  }

  public function getCoveredOrganisations($type = NULL) {
    $query = $this->parDataManager->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');

    if ($type) {
      // @TODO There's a bug where our implementation of lowercase postgres matching
      // causes issues with multiple conditions using of the '=' operator.
      $query->condition('partnership_type', $type, 'CONTAINS');
    }

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    $entities = $query->execute();

    $partnerships = $this->entityTypeManager->getStorage('par_data_partnership')->loadMultiple(array_unique($entities));

    $total = 0;
    foreach ($partnerships as $partnership) {
      $count = 0;

      if ($partnership->isDirect()) {
        $legal_entities = $partnership->getLegalEntity();

        // Count how many legal entities are covered under this direct partnership.
        if ($legal_entities && count($legal_entities) >= 1) {
          $count = count($legal_entities);
        }
        else {
          $count = 1;
        }
      }
      else {
        $members = $partnership->getCoordinatedMember();

        // Count how many members are covered by this partnership.
        if ($members && count($members) >= 1) {
          foreach ($partnership->getCoordinatedMember() as $member) {
            $legal_entities = $member->getLegalEntity();

            // Count how many legal entities are covered under this direct partnership.
            if ($legal_entities && count($legal_entities) >= 1) {
              $count += count($legal_entities);
            }
            else {
              $count += 1;
            }

          }
        }
        else {
          $count = 1;
        }
      }

      $total += $count;
    }

    return $total;
  }

  public function getAuthorities() {
    $query = $this->parDataManager->getEntityQuery('par_data_authority');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    return $query->count()->execute();
  }

  public function getPrimaryAuthorities() {
    $query = $this->parDataManager->getEntityQuery('par_data_partnership')
      ->condition('partnership_status', 'confirmed_rd');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    $entities = $query->execute();

    $partnerships = $this->entityTypeManager->getStorage('par_data_partnership')->loadMultiple(array_unique($entities));

    $authorities = [];
    foreach ($partnerships as $partnership) {
      $primary_authority = $partnership->getAuthority(TRUE);

      if ($primary_authority && !isset($authorities[$primary_authority->uuid()])) {
        $authorities[$primary_authority->uuid()] = $primary_authority->label();
      }
    }

    return count($authorities);
  }

  public function getAuthoritiesWithUser() {
    $query = $this->parDataManager->getEntityQuery('par_data_authority');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    $entities = $query->execute();

    $authorities_with_users = 0;
    $authorities = $this->entityTypeManager->getStorage('par_data_authority')->loadMultiple(array_unique($entities));
    foreach ($authorities as $authority) {
      if ($authority->getPerson(TRUE)) {
        $authorities_with_users++;
      }
    }

    return $authorities_with_users;
  }

  public function getPrimaryAuthorityPercentage() {
    $total = $this->getAuthorities();
    $primary = $this->getPrimaryAuthorities();

    return number_format(((float) ($primary / $total) * 100), 1, '.', '');
  }

  public function getUsers() {
    $query = $this->parDataManager->getEntityQuery('user')
      ->condition('status', 1);

    return $query->count()->execute();
  }

  public function getActiveUsers($roles = NULL, $months = 1) {
    $query = $this->parDataManager->getEntityQuery('user')
      ->condition('status', 1)
      ->condition('access', strtotime("-{$months} months"), '>=');

    if ($roles) {
      $query->condition('roles', $roles, 'IN');
    }

    return $query->count()->execute();
  }

  public function getEnforcements() {
    $query = $this->parDataManager->getEntityQuery('par_data_enforcement_notice');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);


    return $query->count()->execute();
  }

  public function getRecentEnforcements() {
    $query = $this->parDataManager->getEntityQuery('par_data_enforcement_notice')
      ->condition('created', strtotime("-1 months"), '>=');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);


    return $query->count()->execute();
  }

  public function getAverageActions() {
    $query = $this->parDataManager->getEntityQuery('par_data_enforcement_notice');

    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $deleted = $query
      ->orConditionGroup()
      ->condition('deleted', 0)
      ->condition('deleted', NULL, 'IS NULL');

    $query->condition($revoked);
    $query->condition($deleted);

    $entities = $query->execute();

    $enforcement_notices = $this->entityTypeManager->getStorage('par_data_enforcement_notice')->loadMultiple(array_unique($entities));

    $total = 0;
    foreach ($enforcement_notices as $enforcement_notice) {
      $total += count($enforcement_notice->getEnforcementActions());
    }

    return number_format((float) ($total / count($enforcement_notices)), 3, '.', '');
  }

}
