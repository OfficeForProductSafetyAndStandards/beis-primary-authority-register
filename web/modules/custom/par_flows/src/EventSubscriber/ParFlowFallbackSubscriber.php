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


class ParFlowFallbackSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', -800];
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

    // We need a backup route in case all else fails.
    try {
      // getRequiredParams will throw an exception if the route does not exist.
      $redirect_route = $event->getFlow()->getFinalRoute();
    }
    catch (ParFlowException $e) {

    }

    if (isset($redirect_route)) {
      $route_params = $event->getFlow()->getRequiredParams($redirect_route);
      $event->setUrl(Url::fromRoute($redirect_route, $route_params));
    }
  }

}
