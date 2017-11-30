<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewEnforcementSubscriber implements EventSubscriberInterface {

  const MESSAGE_ID = 'new_enforcement_notification_to_primary_authority';

  static function getSubscribedEvents() {
    $events[ParDataEvent::CREATE][] = array('onNewEnforcement', 800);

    return $events;
  }

  /**
   * @param ParDataEvent $event
   */
  public function onNewEnforcement(ParDataEvent $event) {
    $enforcement = $event->getData();

    // Only act on Enforcement Notices that haven't been reviewed.
    if ($enforcement->getEntityTypeId() === 'par_data_enforcement_notice'
      && $enforcement->getRawStatus() === $enforcement->getTypeEntity()->getDefaultStatus()) {

      // We need to get the primary authority for this enforcement.
      $primary_authority = $enforcement->getPrimaryAuthority();
      $primary_authority->getRelationships('par_people');

      // Notify the user.
      \Drupal::service('plugin.manager.par_notifier')->deliver($recipient, self::MESSAGE_ID, 'email', $enforcement);
    };
  }

}
