<?php

namespace Drupal\par_flows;

use Drupal\Core\Entity\EntityTypeInterface;

/**
* Interface for the Par Flow Negotiator.
*/
interface ParFlowNegotiatorInterface {

  /**
   * Returns the current route used to negotiate the flow.
   *
   * @return \Drupal\Core\Routing\CurrentRouteMatch
   *   The current route.
   */
  public function getRoute();

  /**
   * Get's the current user account.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
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
   * Get the key.
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
   * Get the current route name.
   */
  public function getFlowName();

  /**
   * Get the current step name.
   */
  public function getStepId();

}
