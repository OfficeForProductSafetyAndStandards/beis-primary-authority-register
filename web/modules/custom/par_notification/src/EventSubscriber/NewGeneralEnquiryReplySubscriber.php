<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\comment\CommentInterface;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class NewGeneralEnquiryReplySubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_enquiry_response
   */
  const MESSAGE_ID = 'new_enquiry_response';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  static function getSubscribedEvents(): array {
    $events = [];
    if (class_exists(ParDataEvent::class)) {
      $events[EntityInsertEvent::class][] = ['onEvent', 800];
    }

    return $events;
  }

  /**
   * @param EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof ParDataGeneralEnquiry) {
      $this->setEvent($event);

      /** @var CommentInterface $entity */
      $entity = $event->getEntity();
      /** @var ParDataGeneralEnquiry $commented_entity */
      $commented_entity = $entity->getCommentedEntity();
      $par_data_partnership = $commented_entity?->getPartnership(TRUE);

      // Only send messages for active general enquiries.
      if ($commented_entity instanceof ParDataGeneralEnquiry &&
        $par_data_partnership instanceof ParDataPartnership &&
        $commented_entity->isActive()) {

        // Send the message.
        $arguments = [
          '@partnership_label' => strtolower($par_data_partnership->label()),
        ];
        $additional_parameters = ['field_general_enquiry' => $commented_entity];
        $this->sendMessage($arguments, $additional_parameters);
      }
    }
  }

}
