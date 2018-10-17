<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewEnforcementSubscriber implements EventSubscriberInterface {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_enforcement_notification
   */
  const MESSAGE_ID = 'new_enforcement_notification';

  /**
   * The notication plugin that will deliver these notification messages.
   */
  const DELIVERY_METHOD = 'plain_email';

  protected $recipients = [];

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    $events[EntityEvents::insert('par_data_enforcement_notice')][] = ['onNewEnforcement', 800];

    return $events;
  }

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager() {
    return \Drupal::entityTypeManager();
  }

  /**
   * Get the notification service.
   *
   * @return mixed
   */
  public function getNotifier() {
    return \Drupal::service('message_notify.sender');
  }

  /**
   * Get the current user sending the message.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   */
  public function getCurrentUser() {
    return \Drupal::currentUser();
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
          $contacts[$primary_authority_contact->id()] = $primary_authority_contact;
        }
      }
    }

    return $contacts;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onNewEnforcement(EntityEvent $event) {
    /** @var ParDataEntityInterface $par_data_enforcement_notice */
    $par_data_enforcement_notice = $event->getEntity();

    // Load the message template.
    $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
    $message_template = $template_storage->load(self::MESSAGE_ID);

    $message_storage = $this->getEntityTypeManager()->getStorage('message');

    // Get the link to approve this notice.
    $options = ['absolute' => TRUE];
    $enforcement_url = Url::fromRoute('view.par_user_enforcements.enforcement_notices_page', [], $options);

    if (!$message_template) {
      // @TODO Log that the template couldn't be loaded.
      return;
    }

    // Notify the primary authority contact for this Enforcement Notice.
    $contacts = $this->getRecipients($event);
    foreach ($contacts as $contact) {
      if (!isset($this->recipients[$contact->getEmail()])) {

        // Record the recipient so that we don't send them the message twice.
        $this->recipients[$contact->getEmail] = $contact;

        // Create one message per user.
        $message = $message_storage->create([
          'template' => $message_template->id()
        ]);

        // Add contextual information to this message.
        if ($message->hasField('field_enforcement_notice')) {
          $message->set('field_enforcement_notice', $par_data_enforcement_notice);
        }

        // Add some custom arguments to this message.
        $message->setArguments([
          '@enforcement_notice_review' => $enforcement_url->toString(),
        ]);

        // The owner is the user who this message belongs to.
        if ($account) {
          $message->setOwnerId($account->id());
        }
        $message->save();

        // The e-mail address can be overridden if we don't want
        // to send to the message owner set above.
        $options = [
          'mail' => $account->getEmail(),
        ];

        $this->getNotifier()->send($message, $options, self::DELIVERY_METHOD);
      }
    }
  }

}
