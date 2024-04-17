<?php

namespace Drupal\par_flows;

use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Interface for the Par Flow Negotiator.
 */
interface ParFlowNegotiatorInterface {

  /**
   * A helper function to clone a new flow negotiator for a different route.
   *
   * Required for testing access on routes other than the current route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   The route match object that is being checked.
   *
   * @return ParFlowNegotiatorInterface
   */
  public function cloneFlowNegotiator(RouteMatchInterface $route);

  /**
   * Returns the current route used to negotiate the flow.
   *
   * @return \Drupal\Core\Routing\CurrentRouteMatch
   *   The current route.
   */
  public function getRoute();

  /**
   * Gets the current user account.
   *
   * @return \Drupal\user\UserInterface|null
   */
  public function getCurrentUser();

  /**
   * Get the negotiated flow entity.
   *
   * @return \Drupal\par_flows\Entity\ParFlowInterface|null
   */
  public function getFlow();

  /**
   * Get the key for a given form id.
   *
   * @param string $form_id
   *   An optional form_id to get the key for.
   * @param string $state
   *   An optional statestring to get the key for.
   * @param string $flow_name
   *   An optional flow_name to get the key for.
   *
   * @return string
   *   The name of the key for the given form.
   */
  public function getFormKey($form_id, $state = NULL, $flow_name = NULL);

  /**
   * Get the key for a given flow step.
   *
   * @param string $step_id
   *   An optional step_id to get the key for.
   * @param string $state
   *   An optional statestring to get the key for.
   * @param string $flow_name
   *   An optional flow_name to get the key for.
   *
   * @return string
   *   The name of the key for the given form.
   */
  public function getFlowKey($step_id = NULL, $state = NULL, $flow_name = NULL);

  /**
   * Get the key for the flow as a whole, will be the same key on every step of the journey.
   *
   * @param string $state
   *   An optional statestring to get the key for.
   * @param string $flow_name
   *   An optional flow_name to get the key for.
   *
   * @return string
   *   The name of the key for the given form.
   */
  public function getFlowStateKey($state = NULL, $flow_name = NULL);

  /**
   * Get the current route name.
   */
  public function getFlowName();

  /**
   * Get the current step name.
   */
  public function getStepId();

}
