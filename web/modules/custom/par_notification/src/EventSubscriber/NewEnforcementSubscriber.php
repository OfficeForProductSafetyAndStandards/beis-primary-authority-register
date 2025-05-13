<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    if (class_exists(ParDataEvent::class)) {
      $events[EntityInsertEvent::class][] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof ParDataEnforcementNotice) {
      $this->setEvent($event);

      /** @var \Drupal\par_data\Entity\ParDataEnforcementNotice $entity */
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
