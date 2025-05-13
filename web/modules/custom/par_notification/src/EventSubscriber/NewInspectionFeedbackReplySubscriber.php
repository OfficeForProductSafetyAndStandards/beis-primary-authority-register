<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
class NewInspectionFeedbackReplySubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_inspection_feedback_response
   */
  const MESSAGE_ID = 'new_inspection_feedback_response';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    if (class_exists(ParDataEvent::class)) {
      $events[EntityInsertEvent::class][] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof ParDataInspectionFeedback) {
      $this->setEvent($event);

      /** @var \Drupal\comment\CommentInterface $entity */
      $entity = $event->getEntity();
      /** @var \Drupal\par_data\Entity\ParDataInspectionFeedback $commented_entity */
      $commented_entity = $entity->getCommentedEntity();
      $par_data_partnership = $commented_entity?->getPartnership(TRUE);

      // Only send messages for active inspection feedback.
      if ($commented_entity instanceof ParDataInspectionFeedback &&
        $par_data_partnership instanceof ParDataPartnership &&
        $commented_entity->isActive()) {

        // Send the message.
        $arguments = [
          '@partnership_label' => strtolower($par_data_partnership->label()),
        ];
        $additional_parameters = ['field_inspection_feedback' => $commented_entity];
        $this->sendMessage($arguments, $additional_parameters);
      }
    }
  }

}
