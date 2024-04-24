<?php

namespace Drupal\par_transfer_partnerships_flows\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\Route;

/**
 * Checks access for displaying the transfer partnership pages.
 */
class ParFlowAccessCheck implements AccessInterface {

  /**
   * The PAR Data Manager.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  private $parDataManager;

  /**
   * The PAR Flow Negotiator.
   *
   * @var \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  private $flowNegotiator;

  /**
   * CustomAccessCheck constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface
   *   Data Manager Service
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface
   *   Flow Negotiator Service
   */
  public function __construct(ParDataManagerInterface $par_data_manager, ParFlowNegotiatorInterface $flow_negotiator) {
    $this->parDataManager = $par_data_manager;
    $this->flowNegotiator = $flow_negotiator;
  }

  /**
   * Get the Par Flow Negotiator.
   *
   * @return \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return $this->flowNegotiator;
  }

  /**
   * Get the Par Data Manager.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  public function getParDataManager(): ParDataManagerInterface {
    return $this->parDataManager;
  }

  /**
   * Implements accessCallback().
   *
   * The route, the route match object to be checked and the account being checked.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataAuthority $par_data_authority = NULL) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException $e) {

    }

    $user = $account->isAuthenticated() ? User::load($account->id()) : NULL;

    // Check the user has permission to manage the authority.
    if (!$account->hasPermission('bypass par_data membership')
      && (!$user || !$this->parDataManager->isMember($par_data_authority, $user))) {

      return AccessResult::forbidden('User does not have permissions to manage this partnership.');
    }

    // Check that the authority has some partnerships to transfer.
    $number_of_partnerships = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('field_authority', $par_data_authority->id())
      ->count()->execute();
    if ($number_of_partnerships <= 0) {
      return AccessResult::forbidden('This authority has no partnerships to transfer.');
    }

    return AccessResult::allowed();
  }

}
