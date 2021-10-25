<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\par_data\ParDataRelationship;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParNotificationSubscriberBase;

class StaleMemberListWarningSubscriber extends ParNotificationSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/member_list_stale_warning
   */
  const MESSAGE_ID = 'member_list_stale_warning';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Get the custom event, dispatched when a member list needs updating.
    $events[ParDataEvent::customAction('par_data_partnership', 'stale_list_notification')][] = ['onEvent', -100];

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

    // Notify secondary contacts if they've opted-in.
    $contacts = $entity->getOrganisationPeople();

    return $contacts;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    /** @var ParDataEntityInterface par_data_partnership */
    $par_data_partnership = $event->getEntity();

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
        }
        catch (ParNotificationException $e) {
          break;
        }

        // Add contextual information to this message.
        if ($message->hasField('field_partnership')) {
          $message->set('field_partnership', $par_data_partnership);
        }

        // Add some custom arguments to this message.
        $message->setArguments([
          '@partnership_label' => $par_data_partnership ? strtolower($par_data_partnership->label()) : 'partnership',
          '@first_name' => $contact->getFirstName(),
        ]);

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
