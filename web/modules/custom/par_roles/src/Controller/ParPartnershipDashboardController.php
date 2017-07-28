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
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   */
  public function __construct(ConfigEntityStorageInterface $flow_storage) {
    $this->flowStorage = $flow_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_flow')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    // We need to find out what kind of membership
    // the current user has to this partnership.
    if ($this->currentUser()->isAuthenticated()) {
      $current_user_people = \Drupal::entityTypeManager()
        ->getStorage('par_data_person')
        ->loadByProperties(['email' => $this->currentUser()->getEmail()]);
    }

    // We can use this information to figure out what journey to send the user down.
    if ($current_user_people) {
      foreach ($current_user_people as $person) {
        if ($par_data_partnership->isAuthorityMember($person)) {
          // Get the start of an authority journey.
          $route = $this->flowStorage->load('transition_partnership_details')->getRouteByStep(2);
        }
        else if ($par_data_partnership->isOrganisationMember($person)) {
          $route = $this->flowStorage->load('transition_business')->getRouteByStep(2);
        }
      }
    }

    if (!isset($route)) {
      $route = 'view.par_data_transition_journey_1_step_1.dv_journey_1_step_1';
      drupal_set_message(t("We couldn't figure out where to take you because you don't seem to be a member of this partnership."), 'error');
    }

    return $this->redirect($route, $this->getRouteParams());
  }

}

