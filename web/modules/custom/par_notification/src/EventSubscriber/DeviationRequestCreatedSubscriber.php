<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class DeviationRequestCreatedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_deviation_request
   */
  const MESSAGE_ID = 'new_deviation_request';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    if (class_exists('Drupal\par_data\Event\ParDataEvent')) {
      $events[EntityInsertEvent::class][] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof ParDataDeviationRequest) {
      $this->setEvent($event);

      /** @var ParDataDeviationRequest $entity */
      $entity = $event->getEntity();
      $par_data_partnership = $entity?->getPartnership(TRUE);

      // Only send messages for active deviation requests.
      if ($entity instanceof ParDataDeviationRequest &&
        $par_data_partnership instanceof ParDataPartnership) {

        // Send the message.
        $arguments = [
          '@partnership_label' => strtolower($par_data_partnership->label()),
        ];
        $this->sendMessage($arguments);
      }
    }
  }
}
