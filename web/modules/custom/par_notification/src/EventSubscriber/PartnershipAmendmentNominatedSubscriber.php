<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Access\AccessResult;
use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class PartnershipAmendmentNominatedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_amendment_nominated
   */
  const MESSAGE_ID = 'partnership_amendment_nominated';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Confirmation event should fire after a partnership has been confirmed.
    if (class_exists('\ParDataEvent')) {
      $events[ParDataEvent::customAction('par_data_partnership', 'amendment_nominated')][] = ['onEvent', -101];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataPartnership $entity */
    $entity = $event->getEntity();

    $partnership_legal_entities = $entity->getPartnershipLegalEntities(TRUE);

    // Only send the notification if there are legal entities awaiting confirmation.
    if ($entity instanceof ParDataPartnership &&
      !empty($partnership_legal_entities)) {

      // Send the message.
      $arguments = [
        '@partnership' => $entity->label(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
