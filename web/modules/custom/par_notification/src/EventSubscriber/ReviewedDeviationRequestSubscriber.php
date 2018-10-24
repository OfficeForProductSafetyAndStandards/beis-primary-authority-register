<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParNotificationSubscriberBase;

class ReviewedDeviationRequestSubscriber extends ParNotificationSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/reviewed_deviation_request
   */
  const MESSAGE_ID = 'reviewed_deviation_request';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events[ParDataEvent::statusChange('par_data_deviation_request', 'approved')][] = ['onEvent', 800];
    $events[ParDataEvent::statusChange('par_data_deviation_request', 'blocked')][] = ['onEvent', 800];

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
    $contacts = [];

    /** @var ParDataEntityInterface $entity */
    $entity = $event->getEntity();

    // Always notify the primary authority contact.
    if ($enforcing_authority_contact = $entity->getEnforcingPerson(TRUE)) {
      $contacts[$enforcing_authority_contact->id()] = $enforcing_authority_contact;
    }

    // Notify secondary contacts if they've opted-in.
    if ($secondary_contacts = $entity->getEnforcingAuthorityContacts()) {
      foreach ($secondary_contacts as $contact) {
        if (!isset($contacts[$contact->id()]) && $contact->hasNotificationPreference(self::MESSAGE_ID)) {
          $contacts[$contact->id()] = $contact;
        }
      }
    }

    return $contacts;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    /** @var ParDataEntityInterface $par_data_deviation_request */
    $par_data_deviation_request = $event->getEntity();

    $contacts = $this->getRecipients($event);
    foreach ($contacts as $contact) {
      if (!isset($this->recipients[$contact->getEmail()])) {
        // Record the recipient so that we don't send them the message twice.
        $this->recipients[$contact->getEmail] = $contact;
        // Try and get the user account associated with this contact.
        $account = $contact->getOrLookupUserAccount();

        try {
          $message = $this->createMessage();
        }
        catch (ParNotificationException $e) {
          break;
        }

        // Add contextual information to this message.
        if ($message->hasField('field_deviation_request')) {
          $message->set('field_deviation_request', $par_data_deviation_request);
        }

        // The owner is the user who this message belongs to.
        if ($account) {
          $message->setOwnerId($account->id());
        }

        // Send the message.
        $this->sendMessage($message, $contact->getEmail());
      }
    }
  }
}
