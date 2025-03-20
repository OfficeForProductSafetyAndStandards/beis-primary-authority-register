<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class ParAdminGlobalFallbackSubscriber extends ParFlowSubscriberBase {

  /**
   * The ultimate fallback route.
   *
   * @var string $fallback
   */
  protected $fallback = 'par_help_desks_flows.helpdesk_dashboard';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events = [];
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', -901];
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

    // Set the fallback URL.
    $redirect_url = Url::fromRoute($this->fallback);
    $event->setUrl($redirect_url);
  }

}
