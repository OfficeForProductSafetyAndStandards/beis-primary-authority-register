<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
class PartnershipRevocationSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_revocation_notificat
   */
  const MESSAGE_ID = 'partnership_revocation_notificat';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    // Revocation event should fire after most default events to make sure
    // revocation has not been cancelled.
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::statusChange('par_data_partnership', 'revoked')][] = ['onEvent', -100];
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

    // Only send messages for revoked partnerships.
    if ($entity instanceof ParDataPartnership &&
      $entity->isRevoked()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => $entity->label(),
      ];
      $this->sendMessage($arguments);
    }
  }

}
