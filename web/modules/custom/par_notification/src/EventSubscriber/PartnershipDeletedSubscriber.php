<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityPredeleteEvent;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class PartnershipDeletedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_deleted_notification
   */
  const MESSAGE_ID = 'partnership_deleted_notification';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Notify on partnership removal.
    $events[EntityPreDeleteEvent::class][] = ['onEvent', -100];

    return $events;
  }

  /**
   * @param EntityPreDeleteEvent $event
   */
  public function onEvent(EntityPreDeleteEvent $event) {
    if ($event->getEntity() instanceof ParDataPartnership) {
      $this->setEvent($event);

      /** @var ParDataPartnership $entity */
      $entity = $event->getEntity();

      // Only send messages for partnerships.
      if ($entity instanceof ParDataPartnership) {
        // Send the message.
        $arguments = [
          '@partnership_label' => $entity->label(),
        ];
        $this->sendMessage($arguments);
      }
    }
  }
}
