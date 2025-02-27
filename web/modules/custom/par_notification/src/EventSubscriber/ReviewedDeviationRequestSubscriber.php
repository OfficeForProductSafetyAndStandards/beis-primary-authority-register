<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class ReviewedDeviationRequestSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/reviewed_deviation_request
   */
  const MESSAGE_ID = 'reviewed_deviation_request';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    if (class_exists('\ParDataEvent')) {
      $events[ParDataEvent::statusChange('par_data_deviation_request', 'approved')][] = ['onEvent', 800];
      $events[ParDataEvent::statusChange('par_data_deviation_request', 'blocked')][] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataDeviationRequest $entity */
    $entity = $event->getEntity();
    $par_data_partnership = $entity?->getPartnership(TRUE);

    // Only send messages for active deviation requests.
    if ($entity instanceof ParDataDeviationRequest &&
      $par_data_partnership instanceof ParDataPartnership &&
      ($entity->isApproved() || $entity->isBlocked())) {

      // Send the message.
      $arguments = [
        '@partnership_label' => strtolower($par_data_partnership->label()),
      ];
      $this->sendMessage($arguments);
    }
  }
}
