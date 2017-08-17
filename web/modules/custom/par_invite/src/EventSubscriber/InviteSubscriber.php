<?php

/**
 * @file
 * Contains Drupal\event_dispatcher_demo\EventSubscriber\ConfigSubscriber.
 */

namespace Drupal\par_invite\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;

class InviteSubscriber implements EventSubscriberInterface {

  static function getSubscribedEvents() {
    $events['invite_accept'][] = ['onAccept'];
    return $events;
  }

  public function onAccept($event) {
    $event_details = $event->getInviteAcceptEvent();

    if ($event_details['type'] == 'status') {
      $route = new Route('/dv/invite/' . $event_details['invite']->get('reg_code')->getString());
      $event_details['redirect'] = $route;
      $event->setInviteAcceptEvent($event_details);
    };
  }

}
