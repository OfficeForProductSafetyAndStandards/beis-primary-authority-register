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

    $par_flow_obj = $event->getFlow();

    // Get the form ID of the current form being processed so overrides can be set by form.
    $flow_form_processed = $par_flow_obj->getFormIdByStep($par_flow_obj->getCurrentStep());

    switch ($flow_form_processed) {

      case 'par_authority_selection':
        $redirect_route  = $this->cancel_partnership_search;
        break;
      default:
        $redirect_route  = $this->$cancel_fallback_dashboard_route;
    }

    $redirect_url = Url::fromRoute($redirect_route, $par_flow_obj->getRouteParams());

    // Set the ParFlowEvent URL to the fallback cancel operation.
    $event->setUrl($redirect_url);
  }
}


