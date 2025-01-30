<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\Event\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEventInterface;
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
    $events[EntityInsertEvent::class][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event instanceof ParDataEventInterface) {
      $this->event = $event;
    } else {
      // Handle the incompatible event type (log an error, throw an exception, etc.)
      $this->getLogger('PAR')->error('Incompatible event type provided to NewInspectionFeedbackSubscriber::setEvent(). Expected ParDataEventInterface, got @type.', ['@type' => get_class($event)]);
    }

    /** @var ParDataInspectionFeedback $entity */
    $entity = $event->getEntity();

    // Check if the entity is of the correct type.
    if (!$entity instanceof ParDataInspectionFeedback) {
      // Log a message or handle the unexpected entity type as needed.
      $this->getLogger('PAR')->warning('Unexpected entity type received in NewInspectionFeedbackSubscriber::onEvent(). Expected ParDataInspectionFeedback, got @type.', ['@type' => get_class($entity)]);
      return; // Exit early if the entity type is not correct.
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
