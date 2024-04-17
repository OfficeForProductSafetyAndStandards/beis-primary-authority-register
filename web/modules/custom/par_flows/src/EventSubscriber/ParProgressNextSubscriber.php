<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\Core\Url;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\par_flows\Event\ParFlowEvents;

/**
 *
 */
class ParProgressNextSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  public static function getSubscribedEvents() {
    $events[ParFlowEvents::FLOW_SUBMIT][] = ['onEvent', 100];
    return $events;
  }

  /**
   * @param \Drupal\par_flows\Event\ParFlowEventInterface $event
   */
  public function onEvent(ParFlowEventInterface $event) {
    // Ignore if a redirect url has already been found.
    if ($event->getUrl()) {
      return;
    }

    $current_step = $event->getCurrentStep();

    // Check if there is a next step in the journey.
    if ($current_step < count($event->getFlow()->getSteps())) {
      $next_index = ++$current_step;
      $redirect_step = isset($next_index) ? $event->getFlow()->getStep($next_index) : NULL;
      $redirect_route = $redirect_step['route'] ?? NULL;

      if (isset($redirect_route)) {
        $route_params = $event->getFlow()->getRequiredParams($redirect_route);
        $event->setUrl(Url::fromRoute($redirect_route, $route_params));
      }
    }

  }

}
