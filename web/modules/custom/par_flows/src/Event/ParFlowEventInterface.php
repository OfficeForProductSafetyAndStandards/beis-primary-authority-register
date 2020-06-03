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
   * @return string
   */
  public function getCurrentRoute();

  /**
   * @return array
   */
  public function getCurrentStep();

  /**
   * Get the url to redirect to.
   *
   * @return \Drupal\Core\Url
   */
  public function getUrl();

  /**
   * Get the fallback entry point URL.
   */
  public function getEntryUrl();

  /**
   * Set the next url to redirect to.
   *
   * @param \Drupal\Core\Url $url
   *   A url object to redirect to.
   */
  public function setUrl(Url $url);

}
