<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewEnforcementSubscriber implements EventSubscriberInterface {

  const MESSAGE_ID = 'enforcement_created_notice';
  const DELIVERY_METHOD = 'email';

  static function getSubscribedEvents() {
    $events[ParDataEvent::CREATE][] = ['onNewEnforcement', 10];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onNewEnforcement(ParDataEventInterface $event) {
    $enforcement = $event->getData();

    // Only act on Enforcement Notices that haven't been reviewed.
    if ($enforcement->getEntityTypeId() === 'par_data_enforcement_notice'
      && $enforcement->getRawStatus() === $enforcement->getTypeEntity()->getDefaultStatus()) {

      // We need to get the primary authority for this enforcement.
      $primary_authority = $enforcement->getPrimaryAuthority(TRUE);
      foreach ($primary_authority->getPerson() as $person) {
        if ($account = $person->getUserAccount() && $person->getUserAccount()->hasPermission('approve enforcement notice')) {
          // Notify all users in this authority with the permission
          // to approve this enforcement notice.
          \Drupal::service('plugin.manager.par_notifier')->notify($person, self::MESSAGE_ID, self::DELIVERY_METHOD, $enforcement);
        }
      }
    }
  }

}
