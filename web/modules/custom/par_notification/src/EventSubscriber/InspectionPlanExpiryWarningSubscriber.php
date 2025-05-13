<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

/**
 *
 */
class InspectionPlanExpiryWarningSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/inspection_plan_expiry_warning
   */
  const MESSAGE_ID = 'inspection_plan_expiry_warning';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events = [];
    // Revocation event should fire after most default events to make sure
    // revocation has not been cancelled.
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::customAction('par_data_inspection_plan', 'expiry_notification')][] = ['onEvent', -100];
    }

    return $events;
  }

  /**
   * @param \Drupal\par_data\Event\ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var \Drupal\par_data\Entity\ParDataInspectionPlan $entity */
    $entity = $event->getEntity();
    $partnership_relationships = $entity->getRelationships('par_data_partnership');
    $par_data_partnership = !empty($partnership_relationships) ? current($partnership_relationships)->getEntity() : NULL;

    // Only act on approved enforcement notices for direct partnerships.
    if ($entity instanceof ParDataInspectionPlan &&
      $par_data_partnership instanceof ParDataPartnership &&
      $entity->isActive()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => strtolower($par_data_partnership->label()),
        '@inspection_plan_title' => strtolower($entity->getTitle()),
      ];
      $this->sendMessage($arguments);
    }
  }

}
