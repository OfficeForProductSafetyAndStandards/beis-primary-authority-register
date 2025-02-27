<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class NewGeneralEnquirySubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_general_enquiry
   */
  const MESSAGE_ID = 'new_general_enquiry';

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
    if ($event->getEntity() instanceof ParDataGeneralEnquiry) {
      $this->setEvent($event);

      /** @var ParDataGeneralEnquiry $entity */
      $entity = $event->getEntity();
      $par_data_partnership = $entity?->getPartnership(TRUE);

      // Only send messages for active general enquiries.
      if ($entity instanceof ParDataGeneralEnquiry &&
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

}
