<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
class PartnershipNominatedTaskSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID that needs to be completed.
   *
   * @see /admin/structure/message/manage/new_partnership_notification
   */
  const MESSAGE_ID = 'partnership_nominate';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    // Nomination event should fire after a partnership has been nominated.
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::statusChange('par_data_partnership', 'confirmed_rd')][] = ['onPartnershipNominated', 200];
    }

    return $events;
  }

  /**
   * @param \Drupal\par_data\Event\ParDataEventInterface $event
   */
  public function onPartnershipNominated(ParDataEventInterface $event) {
    $this->getMessageExpiryService()->expire($this->getMessages($event));
  }

}
