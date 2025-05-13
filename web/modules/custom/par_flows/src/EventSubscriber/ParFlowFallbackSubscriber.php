<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\Core\Url;

/**
 *
 */
class ParFlowFallbackSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', -800];
    }
    return $events;
  }

  /**
   * @param \Drupal\par_flows\Event\ParFlowEventInterface $event
   */
  public function onEvent(ParFlowEventInterface $event) {
    // We need a backup route in case all else fails.
    foreach ($event->getFlow()->getFinalRoutes() as $redirect_route) {
      try {
        // Ignore if a redirect url has already been found.
        if ($event->getUrl()) {
          continue;
        }

        $route_params = $event->getFlow()->getRequiredParams($redirect_route);
        // Note the URL won't be set if it is not accessible in ParFlowEvent::setUrl()
        $event->setUrl(Url::fromRoute($redirect_route, $route_params));
      }
      catch (ParFlowException) {

      }
    }
  }

}
