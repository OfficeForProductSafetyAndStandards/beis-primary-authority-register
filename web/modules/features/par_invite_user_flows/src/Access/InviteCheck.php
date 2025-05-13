<?php

namespace Drupal\par_invite_user_flows\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_roles\ParRoleManagerInterface;
use Symfony\Component\Routing\Route;

/**
 * Checks access for inviting a person.
 */
class InviteCheck implements AccessInterface {

  /**
   * The access result in the case of failure.
   *
   * @var \Drupal\Core\Access\AccessResultInterface
   */
  protected AccessResultInterface|null $accessResult = NULL;

  /**
   * CustomAccessCheck constructor.
   *
   * @param \Drupal\par_roles\ParRoleManagerInterface $parRoleManager
   *   Role Manager Service.
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $flowNegotiator
   *   Flow Negotiator Service.
   */
  public function __construct(
    /**
     * The PAR Role Manager.
     */
    private readonly ParRoleManagerInterface $parRoleManager,
    /**
     * The PAR Flow Negotiator.
     */
    private readonly ParFlowNegotiatorInterface $flowNegotiator,
  ) {
  }

  /**
   * Get the Par Flow Negotiator.
   *
   * @return \Drupal\par_flows\ParFlowNegotiatorInterface
   *   The Par Flow Negotiator service.
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return $this->flowNegotiator;
  }

  /**
   * Get the Par Data Manager.
   *
   * @return \Drupal\par_roles\ParRoleManagerInterface
   *   The Par Role Manager service.
   */
  public function getParRoleManager(): ParRoleManagerInterface {
    return $this->parRoleManager;
  }

  /**
   * Checks the access to the route.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   * @param \Drupal\par_data\Entity\ParDataPersonInterface $par_data_person
   *   The account being checked.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result as an object.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account, ParDataPersonInterface $par_data_person) {
    try {
      // Get a new flow negotiator that points to the route being checked for access.
      $access_route_negotiator = $this->getFlowNegotiator()->cloneFlowNegotiator($route_match);
    }
    catch (ParFlowException) {

    }

    // There must be a person,
    // and this person can't have an associated user account.
    if (!$par_data_person || $par_data_person->getUserAccount()) {
      $this->accessResult = AccessResult::forbidden('This person already has an associated user account.');
    }
    else {
      $this->accessResult = AccessResult::allowed();
    }

    return $this->accessResult;
  }

}
