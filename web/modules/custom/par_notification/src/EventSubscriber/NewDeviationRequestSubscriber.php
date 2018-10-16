<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewDeviationRequestSubscriber implements EventSubscriberInterface {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_deviation_request
   */
  const MESSAGE_ID = 'new_deviation_request';

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
    $events[EntityEvents::insert('par_data_deviation_request')][] = ['onNewDeviationRequest', 800];

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

  public function getRecipients(EntityEvent $event) {
    /** @var ParDataEntityInterface $par_data_deviation_request */
    $par_data_deviation_request = $event->getEntity();

    // Always notify the primary authority contact.
    $primary_authority_contact = $par_data_deviation_request->getPrimaryAuthorityContacts(FALSE);

    // Notify secondary contacts if they've opted-in.

    // Notify all primary authority contacts if they've opted-in.

    return $contacts;
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onNewDeviationRequest(EntityEvent $event) {
    /** @var ParDataEntityInterface $par_data_deviation_request */
    $par_data_deviation_request = $event->getEntity();

    // Load the message template.
    $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
    $message_template = $template_storage->load(self::MESSAGE_ID);

    $message_storage = $this->getEntityTypeManager()->getStorage('message');

    // Get the link to approve this notice.
    $options = ['absolute' => TRUE];
    $deviation_url = Url::fromRoute('view.par_user_deviation_requests.deviation_requests_page', [], $options);

    if (!$message_template) {
      // @TODO Log that the template couldn't be loaded.
      return;
    }

    // Notify the primary authority contact for this Deviation Request.
    $primary_authority_contact = $par_data_deviation_request->getPrimaryAuthorityContact();
    if ($primary_authority_contact && ($account = $primary_authority_contact->lookupUserAccount()) && $primary_authority_contact->lookupUserAccount()->hasPermission('review deviation request')
      && !isset($this->recipients[$account->id()])) {

      // Record the recipient so that we don't send them the message twice.
      $this->recipients[$account->id()] = $account->getEmail();

      // Create one message per user.
      $message = $message_storage->create([
        'template' => $message_template->id()
      ]);

      // Add contextual information to this message.
      if ($message->hasField('field_deviation_request')) {
        $message->set('field_deviation_request', $par_data_deviation_request);
      }

      // Add some custom arguments to this message.
      $message->setArguments([
        '@deviation_request_review' => $deviation_url->toString(),
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
