<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

class DeviationReviewedTaskSubscriber extends ParEventSubscriberBase  {

  /**
   * The message template ID that needs to be completed.
   *
   * @see /admin/structure/message/manage/new_partnership_notification
   */
  const MESSAGE_ID = 'new_deviation_request';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Deviation request has been reviewed.
    $events[ParDataEvent::statusChange('par_data_deviation_request', 'approved')][] = ['onDeviationRequestReviewed', 200];
    $events[ParDataEvent::statusChange('par_data_deviation_request', 'blocked')][] = ['onDeviationRequestReviewed', 200];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onDeviationRequestReviewed(ParDataEventInterface $event) {
      $this->getMessageExpiryService()->expire($this->getMessages($event));
  }
}
