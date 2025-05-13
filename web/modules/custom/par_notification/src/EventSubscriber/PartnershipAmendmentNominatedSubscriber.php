<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    // Confirmation event should fire after a partnership has been confirmed.
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::customAction('par_data_partnership', 'amendment_nominated')][] = ['onEvent', -101];
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
