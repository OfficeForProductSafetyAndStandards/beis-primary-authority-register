<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
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
    // Revocation event should fire after most default events to make sure
    // revocation has not been cancelled.
    $events[ParDataEvent::statusChange('par_data_partnership', 'deleted')][] = ['onEvent', -100];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataPartnership $entity */
    $entity = $event->getEntity();

    // Only send messages for partnerships.
    // @TODO Confirm whether the partnership entity exists after it has been deleted??
    if ($entity instanceof ParDataPartnership) {

      // Send the message.
      $arguments = [
        '@partnership_label' => $entity->label(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
