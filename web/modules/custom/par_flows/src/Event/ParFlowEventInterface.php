<?php

namespace Drupal\par_flows\Event;

use Drupal\Core\Url;

/**
 * The event is dispatched whenever we need to determine the next flow step.
 */
interface ParFlowEventInterface {

  /**
   * @return \Drupal\par_flows\Entity\ParFlowInterface
   */
  public function getFlow();

  /**
   * @return \Drupal\Core\Routing\RouteMatchInterface
   */
  public function getCurrentRoute();

  /**
   * @return int
   */
  public function getCurrentStep();

  /**
   * @return string
   */
  public function getOperation();

  /**
   * Get the url to redirect to.
   *
   * @return \Drupal\Core\Url
   */
  public function getUrl();

  /**
   * Set the next url to redirect to.
   *
   * @param \Drupal\Core\Url $url
   *   A url object to redirect to.
   */
  public function setUrl(Url $url);

  /**
   * Get the additional data parameters for use in determining the route.
   *
   * @return array
   *   An array of additional data parameters.
   */
  public function getParams();

  /**
   * Allow route options to be set before whether or not a url has been set.
   *
   * @param array $options
   *   An array of route options.
   *   See \Drupal\Core\Url::fromUri() for details.
   */
  public function setRouteOptions(array $options);

}
