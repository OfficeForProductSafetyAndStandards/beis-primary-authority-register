<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParEventSubscriberBase;

class PartnershipAmendmentConfirmedTaskSubscriber extends ParEventSubscriberBase  {

  /**
   * The message template ID that needs to be completed.
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
    // Confirmation event should fire after a partnership has been confirmed.
    $events[ParDataEvent::customAction('par_data_partnership', 'amendment_confirmed')][] = ['onEvent', -101];

    return $events;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    $entity = $event?->getEntity();

    $partnership_legal_entities = (array) $entity?->getPartnershipLegalEntities();
    // Get only the partnership legal entities that are confirmed by the authority..
    $partnership_legal_entities = array_filter($partnership_legal_entities, function ($partnership_legal_entity) {
      return $partnership_legal_entity->getRawStatus() === 'confirmed_authority';
    });

    // Only expire the messages if there aren't any partnership legal entities
    // that are still confirmed by the authority.
    if (empty($partnership_legal_entities)) {
      $messages = $this->getMessages($event);
      $this->getMessageExpiryService()->expire($messages);
    }
  }
}
