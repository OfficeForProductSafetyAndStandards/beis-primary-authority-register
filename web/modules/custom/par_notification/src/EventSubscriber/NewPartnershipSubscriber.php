<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class NewPartnershipSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_partnership_notification
   */
  const MESSAGE_ID = 'new_partnership_notification';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events = [];
    // Confirmation event should fire after a partnership has been confirmed.
    if (class_exists('Drupal\par_data\Event\ParDataEvent')) {
      $events[EntityInsertEvent::class][] = ['onEvent', -101];
    }

    return $events;
  }

  /**
   * @param EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof ParDataPartnership) {
      $this->setEvent($event);

      /** @var ParDataPartnership $entity */
      $entity = $event->getEntity();
      $par_data_authority = $entity?->getAuthority(TRUE);

      // Only send messages for active partnerships.
      if ($entity instanceof ParDataPartnership &&
        $par_data_authority instanceof ParDataAuthority) {

        // Send the message.
        $arguments = [
          '@primary_authority' => $par_data_authority->label(),
        ];
        $this->sendMessage($arguments);
      }
    }
  }
}
