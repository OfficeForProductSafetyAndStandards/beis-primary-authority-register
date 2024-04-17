<?php

namespace Drupal\par_user_block_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 *
 */
trait ParFlowAccessTrait {

  /**
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPerson $par_data_person = NULL, User $user = NULL): AccessResult {
    try {
      // Get a new flow negotiator that points the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    if ($par_data_person) {
      $user = $user ?? $par_data_person->getUserAccount();
    }

    if (!$user) {
      $this->accessResult = AccessResult::forbidden('No user account could be found.');
    }

    // Disable blocking of already blocked users.
    if ($access_route_negotiator->getFlowName() === 'block_user' && $user->isBlocked()) {
      $this->accessResult = AccessResult::forbidden('This user is already blocked.');
    }

    /** @var \Drupal\par_roles\ParRoleManagerInterface $par_role_manager */
    $par_role_manager = \Drupal::service('par_roles.role_manager');

    // Disable blocking of last user in an authority/organisation.
    if ($access_route_negotiator->getFlowName() === 'block_user' && !$par_role_manager->blockable($user)) {
      $this->accessResult = AccessResult::forbidden('This user is the only member of their authority or organisation.');
    }

    // Disable unblocking of users that are already active.
    if ($access_route_negotiator->getFlowName() === 'unblock_user' && $user->isActive()) {
      $this->accessResult = AccessResult::forbidden('This user is already active.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
