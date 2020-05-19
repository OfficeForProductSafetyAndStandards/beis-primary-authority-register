<?php

namespace Drupal\par_flows\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class ProgressRouteCancelOperationSubscriber implements EventSubscriberInterface {


  protected $cancelRoute = 'par_search_partnership_flows.partnership_page';


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

    $par_flow_obj = $event->getFlow();

    $redirect_url = Url::fromRoute($this->cancelRoute, $par_flow_obj->getRouteParams());

    // Set the ParFlowEvent URL to the fallback cancel operation.
    $event->setUrl($redirect_url);
  }
}


