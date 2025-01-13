<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\Event\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

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
  static function getSubscribedEvents() {
    $events[EntityInsertEvent::class][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof ParDataInspectionFeedback) {
      $this->setEvent($event);

      /** @var CommentInterface $entity */
      $entity = $event->getEntity();
      /** @var ParDataInspectionFeedback $commented_entity */
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
