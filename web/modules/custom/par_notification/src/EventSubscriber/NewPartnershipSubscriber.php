<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  public static function getSubscribedEvents() {
    // Confirmation event should fire after a partnership has been confirmed.
    $events[EntityEvents::insert('par_data_partnership')][] = ['onEvent', -101];

    return $events;
  }

  /**
   * @param \Drupal\Core\Entity\EntityEvent $event
   */
  public function onEvent(EntityEvent $event) {
    $this->setEvent($event);

    /** @var \Drupal\par_data\Entity\ParDataPartnership $entity */
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
