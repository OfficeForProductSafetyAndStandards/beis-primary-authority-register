<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class ReviewedEnforcementSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/reviewed_enforcement
   */
  const MESSAGE_ID = 'reviewed_enforcement';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  static function getSubscribedEvents(): array {
    $events = [];
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::statusChange('par_data_enforcement_notice', 'reviewed')][] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataEnforcementNotice $entity */
    $entity = $event->getEntity();
    // Get the partnership for this notice.
    $partnership = $entity->getPartnership(TRUE);

    // Only act on approved enforcement notices for direct partnerships
    if ($entity instanceof ParDataEnforcementNotice &&
      $partnership instanceof ParDataPartnership &&
      !$entity->inProgress() &&
      $partnership->isDirect()) {

      // Send the message.
      $arguments = [
        '@enforced_organisation' => $entity->getEnforcedEntityName(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
