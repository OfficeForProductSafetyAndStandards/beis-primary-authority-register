<?php

namespace Drupal\par_partnership_contact_update_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\EventSubscriber\ParFlowSubscriberBase;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class ParFlowCustomSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', 0];
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

    // Only perform this check for this flow.
    if ($event->getFlow()->id() !== 'partnership_contact_update') {
      return;
    }

    // The final route depends on the 'type' parameter in the route.
    switch ($this->getFlowDataHandler()->getParameter('type')) {
      case 'organisation':
        $redirect_route = 'par_partnership_flows.organisation_details';

        break;

      case 'authority':
        $redirect_route = 'par_partnership_flows.authority_details';

        break;
    }
    try {
      $route_params = $event->getFlow()->getRequiredParams($redirect_route);
      $event->setUrl(Url::fromRoute($redirect_route, $route_params));
    } catch (ParFlowException $e) {

    }
  }

}
