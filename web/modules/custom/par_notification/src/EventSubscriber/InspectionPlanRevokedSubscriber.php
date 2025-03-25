<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class InspectionPlanRevokedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/revoke_inspection_plan
   */
  const MESSAGE_ID = 'revoke_inspection_plan';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  static function getSubscribedEvents(): array {
    $events = [];
    // Revocation event should fire after most default events to make sure
    // revocation has not been cancelled.
    if (class_exists(ParDataEvent::class)) {
      $events[ParDataEvent::statusChange('par_data_inspection_plan', 'revoked')][] = ['onEvent', -100];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataInspectionPlan $entity */
    $entity = $event->getEntity();
    $partnership_relationships = $entity->getRelationships('par_data_partnership');
    $par_data_partnership = !empty($partnership_relationships) ? current($partnership_relationships)->getEntity() : NULL;

    // Only act on approved enforcement notices for direct partnerships
    if ($entity instanceof ParDataInspectionPlan &&
      $par_data_partnership instanceof ParDataPartnership &&
      $entity->isRevoked()) {

      // Send the message.
      $arguments = [
        '@partnership_label' => strtolower($par_data_partnership->label()),
        '@inspection_plan_title' =>  strtolower($entity->getTitle()),
      ];
      $this->sendMessage($arguments);
    }
  }
}
