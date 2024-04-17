<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\Core\Url;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\par_flows\Event\ParFlowEvents;

/**
 *
 */
class ParGlobalFallbackSubscriber extends ParFlowSubscriberBase {

  /**
   * The ultimate fallback route.
   *
   * @var string
   */
  protected $fallback = 'par_dashboards.dashboard';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  public static function getSubscribedEvents() {
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', -900];
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

    // Set the fallback URL.
    $redirect_url = Url::fromRoute($this->fallback);
    $event->setUrl($redirect_url);
  }

}
