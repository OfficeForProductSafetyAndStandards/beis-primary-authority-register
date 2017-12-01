<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewEnforcementSubscriber implements EventSubscriberInterface {

  const MESSAGE_ID = 'enforcement_created_notice';

  static function getSubscribedEvents() {
    $events[ParDataEvent::CREATE][] = array('onNewEnforcement', 800);

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onNewEnforcement(ParDataEvent $event) {
    $enforcement = $event->getData();

    // Only act on Enforcement Notices that haven't been reviewed.
    if ($enforcement->getEntityTypeId() === 'par_data_enforcement_notice'
      && $enforcement->getRawStatus() === $enforcement->getTypeEntity()->getDefaultStatus()) {

      // We need to get the primary authority for this enforcement.
      $primary_authority = $enforcement->getPrimaryAuthority(TRUE);
      foreach ($primary_authority->getPerson() as $person) {
        // Notify the user.
        \Drupal::service('plugin.manager.par_notifier')->deliver($person, self::MESSAGE_ID, 'email', $enforcement);
      }
    }
  }

}
