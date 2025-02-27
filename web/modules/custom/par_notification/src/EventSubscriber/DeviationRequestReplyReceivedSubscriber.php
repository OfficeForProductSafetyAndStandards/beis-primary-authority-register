<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\comment\CommentInterface;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class DeviationRequestReplyReceivedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_deviation_response
   */
  const MESSAGE_ID = 'new_deviation_response';

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
    if ($event->getEntity() instanceof CommentInterface) {
      /** @var CommentInterface $entity */
      $entity = $event->getEntity();

      $this->setEvent($event);

      /** @var ParDataDeviationRequest $commented_entity */
      $commented_entity = $entity->getCommentedEntity();
      $par_data_partnership = $commented_entity?->getPartnership(TRUE);

      // Only send messages for active deviation requests.
      if ($commented_entity instanceof ParDataDeviationRequest &&
        $par_data_partnership instanceof ParDataPartnership &&
        $commented_entity->isActive()) {

        // Send the message.
        $arguments = [
          '@partnership_label' => strtolower($par_data_partnership->label()),
        ];
        $additional_parameters = ['field_deviation_request' => $commented_entity];
        $this->sendMessage($arguments, $additional_parameters);
      }
    }
  }

}
