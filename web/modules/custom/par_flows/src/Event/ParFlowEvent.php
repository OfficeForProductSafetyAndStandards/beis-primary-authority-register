<?php

namespace Drupal\par_flows\Event;

use Drupal\Component\Utility\NestedArray;
use Drupal\par_flows\Event\ParFlowEvents;
use Symfony\Contracts\EventDispatcher\Event;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\par_flows\Entity\ParFlowInterface;

/**
 * Event that is fired when a user logs in.
 */
class ParFlowEvent extends Event implements ParFlowEventInterface {

  /**
   * The par flow.
   *
   * @var ParFlowInterface
   */
  protected $flow;

  /**
   * The current route.
   *
   * @var int
   */
  protected $currentRoute;

  /**
   * The url to redirect to.
   *
   * @var \Drupal\Core\Url
   *   A Url to redirect to.
   */
  private $proceedingUrl;

  /**
   * The current step.
   *
   * @var int
   */
  protected $currentStep;

  /**
   * Additional parameters for use when deciding the route.
   *
   * @var array
   */
  protected $params;

  /**
   * Additional route options to add to any route created.
   *
   * @var array
   */
  protected $options = [];

  /**
   * Constructs the object.
   *
   * @param ParFlowInterface $flow
   *   The flow the event was triggered on.
   * @param RouteMatchInterface $route
   *   The current route.
   * @param string $currentOperation
   *   The operation being performed.
   * @param array $params
   *   An array of additional data for use determining the route.
   */
  public function __construct(ParFlowInterface $flow, RouteMatchInterface $current_route, /**
   * The current operation.
   */
  protected $currentOperation, array $params = []) {
    $this->flow = $flow;
    $this->currentRoute = $current_route;
    $this->currentStep = $flow->getStepByRoute($current_route->getRouteName());
    $this->params = $params;
  }

  /**
   * Get the flow.
   */
  #[\Override]
  public function getFlow() {
    return $this->flow;
  }

  /**
   * Get the route.
   */
  #[\Override]
  public function getCurrentRoute() {
    return $this->currentRoute;
  }

  /**
   * Get the current step.
   */
  #[\Override]
  public function getCurrentStep() {
    return $this->currentStep;
  }

  /**
   * Get the operation.
   */
  #[\Override]
  public function getOperation() {
    return $this->currentOperation;
  }

  /**
   * Get the url to redirect to.
   *
   * @return \Drupal\Core\Url
   */
  #[\Override]
  public function getUrl() {
    $url = $this->proceedingUrl;
    return isset($url) && $url instanceof Url ? $url : NULL;
  }

  /**
   * Set the next url to redirect to.
   *
   * @param Url $url
   *   A url object to redirect to.
   */
  #[\Override]
  public function setUrl(Url $url) {
    // The URL should only be set if it is accessible.
    if (!$url->access() || !$url->isRouted()) {
      return;
    }

    $url->mergeOptions($this->options);
    $this->proceedingUrl = $url;
  }

  /**
   * Get the additional data parameters.
   */
  #[\Override]
  public function getParams() {
    return (array) $this->params;
  }

  /**
   * Get the additional data parameters.
   */
  #[\Override]
  public function setRouteOptions(array $options = []) {
    $this->options = NestedArray::mergeDeep($this->options, $options);
  }

}
