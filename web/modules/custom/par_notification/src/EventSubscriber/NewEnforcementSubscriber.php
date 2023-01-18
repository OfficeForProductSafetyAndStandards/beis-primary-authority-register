<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEventInterface;
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
    $events[EntityEvents::insert('par_data_enforcement_notice')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataEnforcementNotice $entity */
    $entity = $event->getEntity();

    // Only send messages for active deviation requests.
    if ($entity instanceof ParDataEnforcementNotice &&
      $entity->isActive()) {

      // Send the message.
      $arguments = [
        '@enforced_organisation' => $entity->getEnforcedEntityName(),
      ];
      $this->sendMessage($arguments);
    }
  }

}
