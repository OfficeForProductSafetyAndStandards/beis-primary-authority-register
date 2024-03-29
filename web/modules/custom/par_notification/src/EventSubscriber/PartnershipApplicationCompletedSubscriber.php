<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class PartnershipApplicationCompletedSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_confirmed_notificati
   */
  const MESSAGE_ID = 'partnership_confirmed_notificati';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Confirmation event should fire after a partnership has been confirmed.
    $events[ParDataEvent::statusChange('par_data_partnership', 'confirmed_business')][] = ['onEvent', -101];

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
    $par_data_organisation = $entity?->getOrganisation(TRUE);

    // Only send messages for active partnerships.
    if ($entity instanceof ParDataPartnership &&
      $par_data_authority instanceof ParDataAuthority &&
      $par_data_organisation instanceof ParDataOrganisation) {

      // Send the message.
      $arguments = [
        '@organisation' => $par_data_organisation->label(),
        '@primary_authority' => $par_data_authority->label(),
      ];
      $this->sendMessage($arguments);
    }
  }
}
