<?php

/**
 * @file
 * Contains Drupal\event_dispatcher_demo\EventSubscriber\ConfigSubscriber.
 */

namespace Drupal\par_invite\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Url;

class InviteSubscriber implements EventSubscriberInterface {

  static function getSubscribedEvents() {
    $events['invite_accept'][] = ['onAccept'];
    return $events;
  }

  public function onAccept($event) {
    $event_details = $event->getInviteAcceptEvent();

    if ($event_details['type'] == 'status') {
      $path = Url::fromRoute('par_invite.welcome', ['invite' => $event_details['invite']->get('reg_code')->getString()]);
      $route = new Route($path->toString());
      $event_details['redirect'] = $route;
      $event->setInviteAcceptEvent($event_details);
    };
  }

}
