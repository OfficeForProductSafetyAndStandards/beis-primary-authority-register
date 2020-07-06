<?php

namespace Drupal\par_flows\Entity;

use Drupal\Core\Link;

/**
 * The interface for all Flow Entities.
 */
interface ParFlowInterface {

  /**
   * Get the description for this flow.
   *
   * @return string
   *   The description for this entity.
   */
  public function getDescription();

  /**
   * Get the default page title for this flow.
   *
   * @return string
   *   The page title.
   */
  public function getDefaultTitle();

  /**
   * Get the method used to save form data within this flow.
   *
   * @return string
   *   The save method.
   */
  public function getSaveMethod();

  /**
   * Get all the states parameters.
   *
   * @return array
   *   An array of route parameters that can vary the flow data.
   */
  public function getStates();

  /**
   * Get all steps in this flow.
   *
   * @return mixed
   *   An array of all the available steps in a flow.
   */
  public function getSteps();

  /**
   * Get a step by it's index.
   *
   * @param int $index
   *   The step number that is required.
   *
   * @return array
   *   An array with values for the form ID & route
   */
  public function getStep($index);

  /**
   * Get current step.
   *
   * @return NULL|mixed
   *   The key for the current step.
   */
  public function getCurrentStep();

  /**
   * Go to next step.
   *
   * @param string $operation
   *   An optional form operation that can be used to override the redirection.
   *
   * @return mixed
   *   An key for the next step.
   */
  public function getNextStep($operation);

  /**
   * Go to previous step.
   *
   * @param string $operation
   *   An optional form operation that can be used to override the redirection.
   *
   * @return mixed
   *   An key for the previous step.
   */
  public function getPrevStep($operation);

  /**
   * Progress to the next route given an operation being performed.
   *
   * Operations have mildly different impacts within the journey, generally they
   * either progress to the next step or finish the journey. Within this each
   * operation may wish to progress to a different step within the journey or
   * return to a different page entirely.
   *
   * @return string|NULL
   *   The route name to progress to OR NULL if there is no route within the flow to go to.
   */
  public function progressRoute($operation = NULL);

  /**
   * Get the next route.
   *
   * @deprecated use self::progressRoute() instead.
   *
   * @param string $operation
   *   An optional form operation that can be used to override the redirection.
   *
   * @return mixed
   *   A route id for the next step.
   */
  public function getNextRoute($operation);

  /**
   * Get the previous route.
   *
   * @deprecated use self::progressRoute() instead.
   *
   * @param string $operation
   *   An optional form operation that can be used to override the redirection.
   *
   * @return mixed
   *   A route id for the previous step.
   */
  public function getPrevRoute($operation);

  /**
   * Get a step by the form id.
   *
   * @param string $form ID
   *   The form id to lookup.
   *
   * @return mixed
   *   An key for a given step.
   */
  public function getStepByFormId($form_id);

  /**
   * Get a step by the route.
   *
   * @param string $route
   *   The string representing a given route to lookup.
   *
   * @return mixed
   *   An key for a given step.
   */
  public function getStepByRoute($route);

  /**
   * Get a step for redirection given an operation on another step.
   *
   * @param string $index
   *   The step to lookup the operation on.
   * @param string $operation
   *   The operation to use to lookup the redirect step.
   *
   * @return mixed
   *   An key for a given step.
   */
  public function getStepByOperation($index, $operation);

  /**
   * Get route for any given step.
   *
   * @param integer $index
   *   The step number to get a route for.
   *
   * @return NULL|string
   *   The name of the route.
   */
  public function getRouteByStep($index);

  /**
   * Get a form ID for any given step.
   *
   * @param integer $index
   *   The step number to get a form ID for.
   *
   * @return NULL|array
   *   The form ID
   */
  public function getFormIdByStep($index);

  /**
   * Get all the forms in a given flow.
   *
   * @return string[]
   *   An array of strings representing form IDs.
   */
  public function getFlowForms();

  /**
   * Get link for any given step.
   *
   * @param integer $index
   *   The step number to get a link for.
   * @param array $route_params
   *   Additional route parameters to add to the route.
   * @param array $link_options
   *   An array of options to set on the link.
   * @param bool $access
   *   Whether or not an access check should be performed on the link.
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getLinkByStep($index, array $route_params, array $link_options, $access = FALSE);

  /**
   * Get link based on an operation on the current step.
   *
   * @param string $operation
   *   The operation to get the redirection link for.
   * @param array $route_params
   *   Additional route parameters to add to the route.
   * @param array $link_options
   *   An array of options to set on the link.
   * @param bool $access
   *   Whether or not an access check should be performed on the link.
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getLinkByCurrentOperation($operation, array $route_params, array $link_options, $access = FALSE);

  /**
   * Get link based on the next available step.
   *
   * @param string $operation
   *   The operation to get the redirection link for.
   * @param array $route_params
   *   Additional route parameters to add to the route.
   * @param array $link_options
   *   An array of options to set on the link.
   * @param bool $access
   *   Whether or not an access check should be performed on the link.
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getNextLink($operation, array $route_params, array $link_options, $access = FALSE);

  /**
   * Get link based on the previous available step.
   *
   * @param string $operation
   *   The operation to get the redirection link for.
   * @param array $route_params
   *   Additional route parameters to add to the route.
   * @param array $link_options
   *   An array of options to set on the link.
   * @param bool $access
   *   Whether or not an access check should be performed on the link.
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getPrevLink($operation, array $route_params, array $link_options, $access = FALSE);

  /**
   * Get the components for the current step.
   */
  public function getCurrentStepComponents();

  /**
   * Get the form data keys for the current step.
   */
  public function getCurrentStepFormDataKeys();

  /**
   * Get the operations for the current step.
   */
  public function getCurrentStepOperations();

}
