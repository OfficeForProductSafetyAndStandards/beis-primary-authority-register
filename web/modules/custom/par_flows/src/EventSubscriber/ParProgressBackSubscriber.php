<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\Core\Url;

/**
 *
 */
class ParProgressBackSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events[ParFlowEvents::FLOW_BACK] = ['onEvent', 101];
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

    // Check if there is a previous step in the journey.
    if ($current_step !== 0) {
      $prev_index = --$current_step;
      $redirect_step = isset($prev_index) ? $event->getFlow()->getStep($prev_index) : NULL;
      $redirect_route = $redirect_step['route'] ?? NULL;

      if (isset($redirect_route)) {
        $route_params = $event->getFlow()->getRequiredParams($redirect_route);
        $event->setUrl(Url::fromRoute($redirect_route, $route_params));
      }
    }

  }

}
