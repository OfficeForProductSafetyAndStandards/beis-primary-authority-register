<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
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
  public static function getSubscribedEvents() {
    $events[EntityEvents::insert('par_data_general_enquiry')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param \Drupal\Core\Entity\EntityEvent $event
   */
  public function onEvent(EntityEvent $event) {
    $this->setEvent($event);

    /** @var \Drupal\par_data\Entity\ParDataGeneralEnquiry $entity */
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
