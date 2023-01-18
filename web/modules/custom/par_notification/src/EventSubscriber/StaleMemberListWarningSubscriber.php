<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_data\ParDataRelationship;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class StaleMemberListWarningSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/member_list_stale_warning
   */
  const MESSAGE_ID = 'member_list_stale_warning';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Get the custom event, dispatched when a member list needs updating.
    $events[ParDataEvent::customAction('par_data_partnership', 'stale_list_notification')][] = ['onEvent', -100];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataPartnership $entity */
    $entity = $event->getEntity();

    // Only send messages for active coordinated partnerships.
    if ($entity instanceof ParDataPartnership &&
      $entity->isCoordinated() &&
      $entity->isActive()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => $entity->label(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
