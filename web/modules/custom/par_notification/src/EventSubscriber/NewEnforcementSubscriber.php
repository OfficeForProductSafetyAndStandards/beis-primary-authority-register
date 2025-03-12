<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_notification\ParEventSubscriberBase;

class NewEnforcementSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_enforcement_notification
   */
  const MESSAGE_ID = 'new_enforcement_notification';

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
    if ($event->getEntity() instanceof ParDataEnforcementNotice) {
      $this->setEvent($event);

      /** @var ParDataEnforcementNotice $entity */
      $entity = $event->getEntity();

      // Only send messages for active deviation requests.
      if ($entity instanceof ParDataEnforcementNotice) {

        // Send the message.
        $arguments = [
          '@enforced_organisation' => $entity->getEnforcedEntityName(),
        ];
        $this->sendMessage($arguments);
      }
    }
  }

}
