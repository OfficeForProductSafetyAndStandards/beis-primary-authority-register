<?php

namespace Drupal\par_user_block_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;

trait ParFlowAccessTrait {

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPerson $par_data_person = NULL, User $user = NULL) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      if ($user) {
        $this->getFlowDataHandler()->setParameter('user', $user);
      }
      elseif ($par_data_person && $user = $par_data_person->getUserAccount()) {
        $this->getFlowDataHandler()->setParameter('user', $user);
      }

      $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    if (!$user) {
      $this->accessResult = AccessResult::forbidden('No user account could be found.');
    }

    if ($this->getFlowNegotiator()->getFlowName() === 'block_user' && $user->isBlocked()) {
      $this->accessResult = AccessResult::forbidden('This user is already blocked.');
    }

    if ($this->getFlowNegotiator()->getFlowName() === 'unblock_user' && $user->isActive()) {
      $this->accessResult = AccessResult::forbidden('This user is already active.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
