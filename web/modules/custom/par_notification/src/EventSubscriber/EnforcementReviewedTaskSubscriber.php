<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

class EnforcementReviewedTaskSubscriber extends ParEventSubscriberBase  {

  /**
   * The message template ID that needs to be completed.
   *
   * @see /admin/structure/message/manage/new_partnership_notification
   */
  const MESSAGE_ID = 'new_enforcement_notification';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Reviewed enforcement notice.
    if (class_exists('\ParDataEvent')) {
      $events[ParDataEvent::statusChange('par_data_enforcement_notice', 'reviewed')][] = ['onEnforcementNoticeReviewed', 200];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEnforcementNoticeReviewed(ParDataEventInterface $event) {
    $this->getMessageExpiryService()->expire($this->getMessages($event));
  }
}
