<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParNotificationSubscriberBase;

class NewGeneralEnquirySubscriber extends ParNotificationSubscriberBase {

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
  static function getSubscribedEvents() {
    $events[EntityEvents::insert('par_data_general_enquiry')][] = ['onEvent', 800];

    return $events;
  }

  /**
   * Get all the recipients for this notification.
   *
   * @param $event
   *
   * @return ParDataPerson[]
   */
  public function getRecipients(EntityEvent $event) {
    $contacts = [];

    /** @var ParDataEntityInterface $entity */
    $entity = $event->getEntity();

    // Always notify the primary authority contact.
    if ($primary_authority_contact = $entity->getPrimaryAuthorityContacts(TRUE)) {
      $contacts[$primary_authority_contact->id()] = $primary_authority_contact;
    }

    // Notify secondary contacts if they've opted-in.
    if ($secondary_contacts = $entity->getAllPrimaryAuthorityContacts()) {
      foreach ($secondary_contacts as $contact) {
        if (!isset($contacts[$contact->id()]) && $contact->hasNotificationPreference(self::MESSAGE_ID)) {
          $contacts[$contact->id()] = $contact;
        }
      }
    }

    return $contacts;
  }

  /**
   * @param EntityEvent $event
   */
  public function onEvent(EntityEvent $event) {
    /** @var ParDataEntityInterface $par_data_general_enquiry */
    $par_data_general_enquiry = $event->getEntity();

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
        if ($message->hasField('field_general_enquiry')) {
          $message->set('field_general_enquiry', $par_data_general_enquiry);
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
