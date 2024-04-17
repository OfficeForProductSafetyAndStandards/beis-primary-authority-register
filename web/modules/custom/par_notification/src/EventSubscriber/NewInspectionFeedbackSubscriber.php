<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  public static function getSubscribedEvents() {
    $events[EntityEvents::insert('par_data_inspection_feedback')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param \Drupal\Core\Entity\EntityEvent $event
   */
  public function onEvent(EntityEvent $event) {
    $this->setEvent($event);

    /** @var \Drupal\par_data\Entity\ParDataInspectionFeedback $entity */
    $entity = $event->getEntity();
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
