<?php

namespace Drupal\par_flows\Event;

use Drupal\Component\Utility\NestedArray;
use Drupal\par_flows\Event\ParFlowEvents;
use Symfony\Component\EventDispatcher\Event;
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
   * The current operation.
   *
   * @var string
   */
  protected $currentOperation;

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
   * @param string $operation
   *   The operation being performed.
   * @param array $params
   *   An array of additional data for use determining the route.
   */
  public function __construct(ParFlowInterface $flow, RouteMatchInterface $current_route, $operation, array $params = []) {
    $this->flow = $flow;
    $this->currentRoute = $current_route;
    $this->currentStep = $flow->getStepByRoute($current_route->getRouteName());
    $this->currentOperation = $operation;
    $this->params = $params;
  }

  /**
   * Get the flow.
   */
  public function getFlow() {
    return $this->flow;
  }

  /**
   * Get the route.
   */
  public function getCurrentRoute() {
    return $this->currentRoute;
  }

  /**
   * Get the current step.
   */
  public function getCurrentStep() {
    return $this->currentStep;
  }

  /**
   * Get the operation.
   */
  public function getOperation() {
    return $this->currentOperation;
  }

  /**
   * Get the url to redirect to.
   *
   * @return \Drupal\Core\Url
   */
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
  public function setUrl(Url $url) {
    $url->mergeOptions($this->options);
    $this->proceedingUrl = $url;
  }

  /**
   * Get the additional data parameters.
   */
  public function getParams() {
    return (array) $this->params;
  }

  /**
   * Get the additional data parameters.
   */
  public function setRouteOptions(array $options = []) {
    $this->options = NestedArray::mergeDeep($this->options, $options);
  }

}
