<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParEventSubscriberBase;

class NewInspectionPlanSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_inspection_plan
   */
  const MESSAGE_ID = 'new_inspection_plan';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // React to custom reference event bring dispatched.
    $events[ParDataEvent::customAction('par_data_inspection_plan', 'post_create')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * @param EntityEvent $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataInspectionPlan $entity */
    $entity = $event->getEntity();
    $partnership_relationships = $entity->getRelationships('par_data_partnership');
    $par_data_partnership = !empty($partnership_relationships) ? current($partnership_relationships)->getEntity() : NULL;

    // Only send messages for active general enquiries.
    if ($entity instanceof ParDataInspectionPlan &&
      $par_data_partnership instanceof ParDataPartnership &&
      $entity->isActive()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => strtolower($par_data_partnership->label()),
        '@inspection_plan_title' =>  strtolower($entity->getTitle()),
      ];
      $this->sendMessage($arguments);
    }
  }
}
