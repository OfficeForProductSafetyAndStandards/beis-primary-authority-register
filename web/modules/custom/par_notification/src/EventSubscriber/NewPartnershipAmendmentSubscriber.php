<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

class NewPartnershipAmendmentSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_partnership_amendment
   */
  const MESSAGE_ID = 'new_partnership_amendment';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events = [];
    // Confirmation event should fire after a partnership has been confirmed.
    if (class_exists('Drupal\par_data\Event\ParDataEvent')) {
      $events[ParDataEvent::customAction('par_data_partnership', 'amendment_submitted')][] = ['onEvent', -101];
    }

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataPartnership $entity */
    $entity = $event->getEntity();
    $par_data_authority = $entity?->getAuthority(TRUE);

    $partnership_legal_entities = $entity->getPartnershipLegalEntities();
    // Get only the partnership legal entities that are awaiting nomination.
    $partnership_legal_entities = array_filter($partnership_legal_entities, function ($partnership_legal_entity) {
      return $partnership_legal_entity->getRawStatus() === 'confirmed_authority';
    });

    // Only send the notification if there are legal entities awaiting confirmation.
    if ($entity instanceof ParDataPartnership &&
      $par_data_authority instanceof ParDataAuthority &&
      count($partnership_legal_entities) >= 1) {

      // Send the message.
      $arguments = [
        '@primary_authority' => $par_data_authority->label(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
