<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;

class ApprovedEnforcementSubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/approved_enforcement
   */
  const MESSAGE_ID = 'approved_enforcement';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events[ParDataEvent::statusChange('par_data_enforcement_notice', 'reviewed')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * Get all the recipients for this notification.
   *
   * @param $event
   *
   * @return ParDataPerson[]
   */
  public function getRecipients(ParDataEventInterface $event) {
    /** @var ParDataEntityInterface $entity */
    $entity = $event->getEntity();

    // Get the contact information for the primary business.
    $contacts = $entity->getOrganisationContacts();

    return $contacts;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    /** @var ParDataEnforcementNotice $par_data_enforcement_notice */
    $par_data_enforcement_notice = $event->getEntity();

    // Get the partnership for this notice.
    $partnership = $par_data_enforcement_notice->getPartnership(TRUE);

    // Only act on approved enforcement notices for direct partnerships
    if ($par_data_enforcement_notice instanceof ParDataEnforcementNotice &&
      $partnership instanceof ParDataPartnership &&
      $par_data_enforcement_notice->isApproved() &&
      $partnership->isDirect()) {

      // Create the message.
      try {
        $message = $this->getMessageHandler()->createMessage(static::MESSAGE_ID);
      } catch (ParNotificationException $e) {
        return;
      }

      if ($message instanceof MessageInterface) {
        // Add contextual information to this message.
        if ($message->hasField('field_enforcement_notice')) {
          $message->set('field_enforcement_notice', $par_data_enforcement_notice);
        }

        // Add some custom arguments to this message.
        $arguments = array_merge($message->getArguments(), [
          '@enforced_organisation' => $par_data_enforcement_notice->getEnforcedEntityName(),
        ]);
        $message->setArguments($arguments);

        // Save the message (this will also send it).
        $message->save();
      }
    }
  }
}
