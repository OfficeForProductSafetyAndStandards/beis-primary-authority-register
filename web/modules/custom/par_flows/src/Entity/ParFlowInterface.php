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
   * @return mixed
   *   An key for the next step.
   */
  public function getNextStep();

  /**
   * Go to previous step.
   *
   * @return mixed
   *   An key for the previous step.
   */
  public function getPrevStep();

  /**
   * Get the next route.
   *
   * @return mixed
   *   A route id for the next step.
   */
  public function getNextRoute();

  /**
   * Get the previous route.
   *
   * @return mixed
   *   A route id for the previous step.
   */
  public function getPrevRoute();

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
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getLinkByStep($index, array $route_params, array $link_options);

}
