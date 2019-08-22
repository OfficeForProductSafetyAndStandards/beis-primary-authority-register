<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParNotificationSubscriberBase;

class ReviewedEnforcementSubscriber extends ParNotificationSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/reviewed_enforcement
   */
  const MESSAGE_ID = 'reviewed_enforcement';

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
    /** @var ParDataEntityInterface $par_data_enforcement_notice */
    $par_data_enforcement_notice = $event->getEntity();

    foreach ($par_data_enforcement_notice->getEnforcementActions() as $delta => $par_data_enforcement_action) {

      // Don't notify about referred actions.
      if ($par_data_enforcement_action->isReferred()) {
        continue;
      }

      // Get the contacts for this notification and build the message.
      $contacts = $this->getRecipients($event);
      foreach ($contacts as $contact) {
        if (!isset($this->recipients[$contact->getEmail()])) {
          // Record the recipient so that we don't send them the message twice.
          $this->recipients[$contact->getEmail] = $contact;
          // Try and get the user account associated with this contact.
          $account = $contact->getUserAccount();

          try {
            /** @var Message $message */
            $message = $this->createMessage();
          } catch (ParNotificationException $e) {
            break;
          }

          // Add contextual information to this message.
          if ($message->hasField('field_enforcement_notice')) {
            $message->set('field_enforcement_notice', $par_data_enforcement_notice);
          }

          // Add some custom arguments to this message.
          $message->setArguments([
            '@first_name' => $contact->getFirstName(),
            '@enforced_organisation' => $par_data_enforcement_notice->getEnforcedEntityName(),
          ]);

          // The owner is the user who this message belongs to.
          if ($account) {
            $message->setOwnerId($account->id());
          }

          // Send the message.
          $this->sendMessage($message, $contact->getEmail());
        }
      }

      // Only send the message once per enforcement notice.
      break;

    }
  }
}
