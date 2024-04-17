<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\ParFlowException;

/**
 *
 */
class ParProgressOperationSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  public static function getSubscribedEvents() {
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', 200];
    }
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

    if (NULL !== $event->getOperation()) {
      // Catch any exceptions, it is not required to return any url.
      try {
        $url = $event->getFlow()->goto($event->getOperation(), $event->getParams());

        $event->setUrl($url);
      }
      catch (ParFlowException $e) {

      }
    }

  }

}
