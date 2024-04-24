<?php

namespace Drupal\par_partnership_contact_update_flows;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * {@inheritdoc}
 */
trait ParFlowAccessTrait {

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPartnership $par_data_partnership = NULL, $type = NULL, ParDataPerson $par_data_person = NULL): AccessResult {
    try {
      // New flow negotiator that points the the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }
    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    switch ($type) {
      case 'authority':
        if (!$account->hasPermission('update partnership authority contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to update authority contacts.');
        }

        // Check the user has permission to manage the current authority.
        if (!$account->hasPermission('bypass par_data membership')
          && !$this->getParDataManager()->isMember($par_data_partnership->getAuthority(TRUE), $user)) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to remove authority contacts from this partnership.');
        }

        break;

      case 'organisation':
        if (!$account->hasPermission('update partnership organisation contact')) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to update organisation contacts.');
        }

        // Check the user has permission to manage the current organisation.
        if (!$account->hasPermission('bypass par_data membership')
          && !$this->getParDataManager()->isMember($par_data_partnership->getOrganisation(TRUE), $user)) {
          $this->accessResult = AccessResult::forbidden('User does not have permissions to remove authority contacts from this partnership.');
        }

        break;

      default:
        $this->accessResult = AccessResult::forbidden('A valid contact type must be choosen.');
    }

    return parent::accessCallback($route, $route_match, $account);
  }

}
