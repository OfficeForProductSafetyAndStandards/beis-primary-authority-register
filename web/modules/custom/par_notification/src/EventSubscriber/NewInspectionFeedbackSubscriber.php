<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class NewInspectionFeedbackSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_inspection_feedback
   */
  const MESSAGE_ID = 'new_inspection_feedback';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    if (class_exists('\ParDataEvent')) {
      $events[EntityInsertEvent::class] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param \Drupal\Core\Entity\Event\EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    $entity = $event->getEntity();

    // 1. Check if the entity is a ParDataInspectionFeedback
    if (!$entity instanceof ParDataInspectionFeedback) {
      return; // Exit if not the correct entity type
    }

    $par_data_partnership = $entity?->getPartnership(TRUE);

    // Only send messages for active general enquiries.
    if ($entity instanceof ParDataInspectionFeedback &&
      $par_data_partnership instanceof ParDataPartnership &&
      $entity->isActive()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => strtolower($par_data_partnership->label()),
      ];
      $this->sendMessage($arguments);
    }
  }

}
