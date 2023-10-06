<?php

namespace Drupal\par_partnership_contact_remove_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataPartnership;
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
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, $type = NULL): AccessResult {
    try {
      // Get a new flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    } catch (ParFlowException $e) {

    }

    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    switch ($type) {
      case 'authority':
        if (!$account->hasPermission('remove partnership authority contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to remove authority contacts.');
        }
        $people = $par_data_partnership->getAuthorityPeople();

        // Check the user has permission to manage the current authority.
        if (!$account->hasPermission('bypass par_data membership')
          && !$this->getParDataManager()->isMember($par_data_partnership->getAuthority(TRUE), $user)) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to remove authority contacts from this partnership.');
        }

        break;

      case 'organisation':
        if (!$account->hasPermission('remove partnership organisation contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to remove organisation contacts.');
        }
        $people = $par_data_partnership->getOrganisationPeople();

        // Check the user has permission to manage the current organisation.
        if (!$account->hasPermission('bypass par_data membership')
          && !$this->getParDataManager()->isMember($par_data_partnership->getOrganisation(TRUE), $user)) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to remove organisation contacts from this partnership.');
        }

        break;

      default:
        $this->accessResult = AccessResult::forbidden('A valid contact type must be choosen.');
    }

    // This can't be the last contact left on the partnership.
    if (count($people) <= 1) {
      $this->accessResult = AccessResult::forbidden('You cannot remove the last contact on this partnership.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }
}
