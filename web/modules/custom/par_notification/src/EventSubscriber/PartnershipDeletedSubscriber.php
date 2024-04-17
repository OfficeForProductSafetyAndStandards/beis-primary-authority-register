<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  public static function getSubscribedEvents() {
    // Notify on partnership removal.
    $events[EntityEvents::predelete('par_data_partnership')][] = ['onEvent', -100];

    return $events;
  }

  /**
   * @param \Drupal\Core\Entity\EntityEvent $event
   */
  public function onEvent(EntityEvent $event) {
    $this->setEvent($event);

    /** @var \Drupal\par_data\Entity\ParDataPartnership $entity */
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
