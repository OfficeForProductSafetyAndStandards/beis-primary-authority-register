<?php

namespace Drupal\par_roles\Controller;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller for the PAR Partnership Dashboard.
 *
 * {@deprecated}
 */
class ParPartnershipDashboardController extends ControllerBase {

  use ParRedirectTrait;

  /**
   * The flow entity storage class, for loading flows.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $flowStorage;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   */
  public function __construct(ConfigEntityStorageInterface $flow_storage, ParDataManagerInterface $par_data_manager) {
    $this->flowStorage = $flow_storage;
    $this->parDataManager = $par_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_flow'),
      $container->get('par_data.manager')
    );
  }

  /**
   * Returns the PAR data manager.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   *   Get the logger channel to use.
   */
  public function getParDataManager() {
    return $this->parDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    // We need to find out what kind of membership
    // the current user has to this partnership.
    if ($this->currentUser()->isAuthenticated()) {
      $account = User::Load($this->currentUser()->id());

      $par_data_manager = $this->getParDataManager();
      if ($par_data_manager->isMemberOfAuthority($account) && $account->hasRole('par_authority')) {
        // Get the start of an authority journey.
        $route = $this->flowStorage->load('transition_partnership_details')->getRouteByStep(2);
      } else if ($par_data_manager->isMemberOfCoordinator($account) && $account->hasRole('par_coordinator')) {
        // Get the start of an authority journey.
        $route = $this->flowStorage->load('transition_coordinator')->getRouteByStep(2);
      } else if ($par_data_manager->isMemberOfBusiness($account) && $account->hasRole('par_business')) {
        // Get the start of an authority journey.
        $route = $this->flowStorage->load('transition_business')->getRouteByStep(2);
      }
    }
    
    if (!isset($route)) {
      $route = 'view.par_data_transition_journey_1_step_1.dv_journey_1_step_1';
      drupal_set_message(t("You don't seem to be a part of this partnership, please contact the Help Desk if this is in error."), 'error');
    }

    return $this->redirect($route, $this->getRouteParams());
  }

}

