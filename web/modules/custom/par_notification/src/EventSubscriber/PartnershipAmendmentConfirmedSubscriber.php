<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class PartnershipAmendmentConfirmedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_amendment_confirmed
   */
  const MESSAGE_ID = 'partnership_amendment_confirmed';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Confirmation event should fire after a partnership has been confirmed.
    $events[ParDataEvent::customAction('par_data_partnership', 'amendment_confirmed')][] = ['onEvent', -101];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $this->setEvent($event);

    /** @var ParDataPartnership $entity */
    $entity = $event->getEntity();
    $par_data_organisation = $entity?->getOrganisation(TRUE);

    $partnership_legal_entities = $entity->getPartnershipLegalEntities();
    // Get only the partnership legal entities that are awaiting nomination.
    $partnership_legal_entities = array_filter($partnership_legal_entities, function ($partnership_legal_entity) {
      return $partnership_legal_entity->getRawStatus() === 'confirmed_business';
    });

    // Only send the notification if there are legal entities awaiting confirmation.
    if ($entity instanceof ParDataPartnership &&
      $par_data_organisation instanceof ParDataOrganisation &&
      count($partnership_legal_entities) >= 1) {

      // Send the message.
      $arguments = [
        '@organisation' => $par_data_organisation->label(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
