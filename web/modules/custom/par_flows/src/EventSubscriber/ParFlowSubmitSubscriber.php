<?php

namespace Drupal\par_flows\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\Core\Url;


class ParFlowSubmitSubscriber implements EventSubscriberInterface {

  use ParControllerTrait;


  protected $next_fallback_dashboard_route = 'par_dashboards.dashboard';


  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {

    // Listen for forms being submitted with the "next" $submit_action.
    $events[ParFlowEvent::getCustomEvent('next')][] = ['onEvent', -100];
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
    // If the next route could be found in the flow then
    // return to the entry route if one was specified.
    if (!isset($redirect_url)  && $url = $this->getEntryUrl()) {
      $route_name = $url->getRouteName();
      $route_params = $url->getRouteParameters();
    }

    // We need a backup route in case all else fails.
    if (!isset($route_name)) {
      $route_name = $this->next_fallback_dashboard_route;
      $route_params = [];
    }

    // Determine whether to use the 'destination' query parameter
    // to determine redirection preferences.
    $options = [];
  //  $query = $this->getRequest()->query;
  //  if ($this->skipQueryRedirection && $query->has('destination')) {
    //  $options['query']['destination'] = $query->get('destination');
     // $query->remove('destination');
    //}


    $redirect_url = isset($route_name) && isset($route_params) ? Url::fromRoute($route_name, $route_params) : [];

    // Set the ParFlowEvent URL to the fallback form submission default fallback URL.
    $event->setUrl($redirect_url);
  }
}


