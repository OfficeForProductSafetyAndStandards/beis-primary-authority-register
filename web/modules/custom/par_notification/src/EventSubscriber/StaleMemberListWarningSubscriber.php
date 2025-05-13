<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    // Get the custom event, dispatched when a member list needs updating.
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::customAction('par_data_partnership', 'stale_list_notification')][] = ['onEvent', -100];
    }

    return $events;
  }

  /**
   * @param \Drupal\par_data\Event\ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var \Drupal\par_data\Entity\ParDataPartnership $entity */
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
