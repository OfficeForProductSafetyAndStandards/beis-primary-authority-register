<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class ParProgressOperationSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events = [];
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', 200];
    }
    return $events;
  }

  /**
   * @param ParFlowEventInterface $event
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
