<?php

namespace Drupal\par_flows\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class ParFlowCancelSubscriber implements EventSubscriberInterface {

  protected $cancel_partnership_search = 'par_search_partnership_flows.partnership_page';

  protected $cancel_fallback_dashboard_route = 'par_dashboards.dashboard';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {

    $events[ParFlowEvent::getCustomEvent('cancel')][] = ['onEvent', -100];
    return $events;
  }

  /**
   * @param ParFlowEventInterface $event
   */
  public function onEvent(ParFlowEventInterface $event) {

    $redirect_url = $event->getUrl();

    // If the redirect URL has already been set by the previous conditions this event subscriber will not set a fallback default.
    if (isset($redirect_url) && ($redirect_url instanceof Url)) {
      return;
    }

    // If the cancel route could be found in the flow then
    // return to the entry route if one was specified.
    if (!isset($redirect_url)  && $url = $event->getEntryUrl()) {
      $redirect_route = $url->getRouteName();
      $route_params = $url->getRouteParameters();
    }

    // We need a backup route in case all else fails.
    if (!isset($redirect_route)) {
      $redirect_route = $this->cancel_fallback_dashboard_route;
      $route_params = [];
    }

    $redirect_url = isset($redirect_route) && isset($route_params) ? Url::fromRoute($redirect_route, $route_params) : NULL;

    // Set the ParFlowEvent URL to the fallback cancel operation.
    $event->setUrl($redirect_url);
  }
}


