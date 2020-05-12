<?php

namespace Drupal\par_flows\Event;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\par_flows\Entity\ParFlowInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when a user logs in.
 */
class ParFlowEvent extends Event implements ParFlowEventInterface {

  const FLOW_CANCEL = 'par_flows_alter_cancel';
  const FLOW_BACK = 'par_flows_alter_back';
  const FLOW_SUBMIT = 'par_flows_alter_submit';

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
   * Constructs the object.
   *
   * @param ParFlowInterface $flow
   *   The flow the event was triggered on.
   * @param RouteMatchInterface $route
   *   The current route.
   * @param Url $url
   *   The matched URL.
   */
  public function __construct(ParFlowInterface $flow, RouteMatchInterface $current_route, Url $url = NULL) {
    $this->flow = $flow;
    $this->currentRoute = $current_route;
    $this->currentStep = $flow->getStepByRoute($current_route->getRouteName());

    if ($url) {
      $this->setUrl($url);
    }
  }

  /**
   * Static method for generating an event name based on the operation.
   */
  public static function getCustomEvent($operation) {
    // Return event names for specific operations.
    switch ($operation) {
      case 'back':
        return self::FLOW_BACK;

        break;

      case 'cancel':
        return self::FLOW_CANCEL;

        break;

    }

    return implode(':', [self::FLOW_SUBMIT, $operation]);
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
   * Get the url to redirect to.
   *
   * @return \Drupal\Core\Url
   */
  public function getUrl() {
    $url = $this->proceedingUrl;
    return isset($url) && $url instanceof Url ? $this->proceedingUrl : NULL;
  }

  /**
   * Set the next url to redirect to.
   *
   * @param Url $url
   *   A url object to redirect to.
   */
  public function setUrl(Url $url) {
    $this->proceedingUrl = $url;
  }

}
