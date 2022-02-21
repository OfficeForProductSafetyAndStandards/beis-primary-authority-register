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

class InspectionPlanExpiryWarningSubscriber extends ParNotificationSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/inspection_plan_expiry_warning
   */
  const MESSAGE_ID = 'inspection_plan_expiry_warning';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Revocation event should fire after most default events to make sure
    // revocation has not been cancelled.
    $events[ParDataEvent::customAction('par_data_inspection_plan', 'expiry_notification')][] = ['onEvent', -100];

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

    /** @var ParDataEntityInterface $par_data_inspection_plan */
    $par_data_inspection_plan = $event->getEntity();
    /** @var ParDataRelationship[] $partnership_relationships */
    $partnership_relationships = $par_data_inspection_plan->getRelationships('par_data_partnership');

    foreach ($partnership_relationships as $relationship) {
      $par_data_partnership = $relationship->getEntity();

      // Always notify the primary authority contacts.
      if ($primary_authority_contacts = $par_data_partnership->getAuthorityPeople()) {
        foreach ($primary_authority_contacts as $contact) {
          if (!isset($contacts[$contact->id()])) {
            $contacts[$contact->id()] = $contact;
          }
        }
      }

      // Notify secondary contacts at the authority if there are any.
      if ($authority = $par_data_partnership->getAuthority(TRUE)) {
        foreach ($authority->getPerson() as $contact) {
          if (!isset($contacts[$contact->id()]) && $contact->hasNotificationPreference(self::MESSAGE_ID)) {
            $contacts[$contact->id()] = $contact;
          }
        }
      }
    }

    return $contacts;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onEvent(ParDataEventInterface $event) {
    /** @var ParDataEntityInterface par_data_inspection_plan */
    $par_data_inspection_plan = $event->getEntity();
    /** @var ParDataRelationship[] $partnership_relationships */
    $partnership_relationships = $par_data_inspection_plan->getRelationships('par_data_partnership');
    $par_data_partnership = !empty($partnership_relationships) ? current($partnership_relationships)->getEntity() : NULL;

    $contacts = $this->getRecipients($event);
    foreach ($contacts as $contact) {
      if (!isset($this->recipients[$contact->getEmail()])) {
        // Record the recipient so that we don't send them the message twice.
        $this->recipients[$contact->getEmail()] = $contact;
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
        if ($message->hasField('field_inspection_plan')) {
          $message->set('field_inspection_plan', $par_data_inspection_plan);
        }

        // Add some custom arguments to this message.
        $message->setArguments([
          '@partnership_label' => $par_data_partnership ? strtolower($par_data_partnership->label()) : 'partnership',
          '@inspection_plan_title' =>  $par_data_inspection_plan ? strtolower($par_data_inspection_plan->getTitle()) : 'inspection plan',
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
