<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\message\Entity\Message;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParNotificationSubscriberBase;

class NewInspectionPlanSubscriber extends ParNotificationSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_inspection_plan
   */
  const MESSAGE_ID = 'new_inspection_plan_notification';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Confirmation event should fire after a partnership has been confirmed.
    $events[EntityEvents::insert('par_data_inspection_plan')][] = ['onEvent', -101];

    return $events;
  }

  /**
   * Get all the recipients for this notification.
   *
   * @param EntityEvent $event
   *
   * @return ParDataPerson[]
   */
  public function getRecipients(EntityEvent $event) {
    $contacts = [];

    /** @var ParDataEntityInterface $entity */
    $entity = $event->getEntity();

    $par_data_partnership = $entity->getRelationships('par_data_partnership');

    // This is a new inspection plan that has been created on an active partnership.
    // There cannot be multiple partnerships referenced for this inspection plan.
    foreach ($par_data_partnership as $uuid => $relationship) {
      if (is_object($relationship->getEntity())) {
        $par_data_partnership = $relationship->getEntity();
      }
    }

    // Always notify the primary authority contacts.
    if ($primary_authority_contacts = $par_data_partnership->getAuthorityPeople()) {
      foreach ($primary_authority_contacts as $contact) {
        if (!isset($contacts[$contact->id()])) {
          $contacts[$contact->id()] = $contact;
        }
      }
    }
    // Always notify the primary organisation contacts.
    if ($primary_organisation_contact = $par_data_partnership->getOrganisationPeople()) {
      foreach ($primary_organisation_contact as $contact) {
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
    // Notify secondary contacts at the organisation if there are any.
    if ($organisation = $par_data_partnership->getOrganisation(TRUE)) {
      foreach ($organisation->getPerson() as $contact) {
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
    /** @var ParDataEntityInterface par_data_inspection_plan */
    $par_data_inspection_plan = $event->getEntity();


    $par_data_partnership = $par_data_inspection_plan->getRelationships('par_data_partnership');

    // This is a new inspection plan that has been created on an active partnership.
    // There cannot be multiple partnerships referenced for this inspection plan.
    foreach ($par_data_partnership as $uuid => $relationship) {
      if (is_object($relationship->getEntity())) {
        $par_data_partnership = $relationship->getEntity();
      }
    }

  //  $par_data_partnership = $par_data_inspection_plan ? $par_data_inspection_plan->getPartnership(TRUE) : NULL;

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
