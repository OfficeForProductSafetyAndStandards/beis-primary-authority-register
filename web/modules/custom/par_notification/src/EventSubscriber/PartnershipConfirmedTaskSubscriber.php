<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

class PartnershipConfirmedTaskSubscriber extends ParEventSubscriberBase  {

  /**
   * The message template ID that needs to be completed.
   *
   * @see /admin/structure/message/manage/new_partnership_notification
   */
  const MESSAGE_ID = 'new_partnership_notification';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events = [];
    // Confirmation event should fire after a partnership has been confirmed.
    if (class_exists('Drupal\par_data\Event\ParDataEvent')) {
      $events[ParDataEvent::statusChange('par_data_partnership', 'confirmed_business')][] = ['onPartnershipConfirmed', 200];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onPartnershipConfirmed(ParDataEventInterface $event) {
    $this->getMessageExpiryService()->expire($this->getMessages($event));
  }
}
