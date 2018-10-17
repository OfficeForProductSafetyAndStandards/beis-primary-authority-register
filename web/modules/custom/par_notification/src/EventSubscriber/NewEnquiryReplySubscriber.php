<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\comment\CommentInterface;
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

class NewEnquiryReplySubscriber implements EventSubscriberInterface {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/new_response
   */
  const MESSAGE_ID = 'new_response';

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
    $events[EntityEvents::insert('comment')][] = ['onNewReply', 800];

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

    /** @var CommentInterface $comment */
    $comment = $event->getEntity();
    /** @var ParDataEntityInterface $entity */
    $entity = $comment->getCommentedEntity();

    // Always notify the primary authority contact.
    if ($primary_authority_contact = $entity->getPrimaryAuthorityContacts(TRUE)) {
      $contacts[$primary_authority_contact->id()] = $primary_authority_contact;
    }
    if ($enforcing_authority_contact = $entity->getEnforcingPerson(TRUE)) {
      $contacts[$enforcing_authority_contact->id()] = $enforcing_authority_contact;
    }

    // Notify secondary contacts if they've opted-in.
    $secondary_primary_authority_contacts = $entity->getAllPrimaryAuthorityContacts();
    $secondary_enforcing_authority_contacts = $entity->getEnforcingAuthorityContacts();
    if ($secondary_contacts = $entity->combineContacts($secondary_primary_authority_contacts, $secondary_enforcing_authority_contacts)) {
      foreach ($secondary_contacts as $contact) {
        if (!isset($contacts[$contact->id()]) && $contact->hasNotificationPreference(self::MESSAGE_ID)) {
          $contacts[$primary_authority_contact->id()] = $primary_authority_contact;
        }
      }
    }

    // Remove the contact if they are they created this response.
    for ($i = 0; $i > count($contacts); $i++) {
      $contact = &$contacts[$i];
      $account = $contact->lookupUserAccount();
      if ($account && $account->id() !== $comment->getOwnerId()) {

        unset($contact);
      }
    }

    return array_filter($contacts);
  }

  /**
   * @param ParDataEventInterface $event
   */
  public function onNewReply(EntityEvent $event) {
    /** @var CommentInterface $comment */
    $comment = $event->getEntity();
    $par_data_entity = $comment->getCommentedEntity();

    // Load the message template.
    $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
    $message_template = $template_storage->load(self::MESSAGE_ID);

    $message_storage = $this->getEntityTypeManager()->getStorage('message');

    // Get the response entity type to customize the message,
    // without the 'par' prefix.
    $response_entity_type = str_replace('par ', '', $par_data_entity->getEntityType()->getLowercaseLabel());

    // Get the link to approve this notice.
    $options = ['absolute' => TRUE];
    $response_url = Url::fromRoute('par_dashboards.dashboard', [], $options);

    // Get the default permission.
    $permission = 'post comments';

    // Customise link and permission based on enquiry type.
    switch ($response_entity_type) {
      case 'deviation request':
        $permission = 'view deviation request';
        $response_url = Url::fromRoute('view.par_user_deviation_requests.deviation_requests_page', [], $options);

        break;

      case 'inspection feedback':
        $permission = 'view inspection feedback';
        $response_url = Url::fromRoute('view.par_user_inspection_feedback.inspection_feedback_page', [], $options);

        break;

      case 'general enquiry':
        $permission = 'view general enquiry';
        $response_url = Url::fromRoute('view.par_user_general_enquiries.general_enquiries_page', [], $options);

        break;

    }

    if (!$message_template) {
      // @TODO Log that the template couldn't be loaded.
      return;
    }

    $contacts = $this->getRecipients($event);
    foreach ($contacts as $contact) {
      // Create one message per user.
      $message = $message_storage->create([
        'template' => $message_template->id()
      ]);

      // Add contextual information to this message.
      if ($message->hasField('field_comment')) {
        $message->set('field_comment', $comment);
      }

      // Add some custom arguments to this message.
      $message->setArguments([
        '@response_view' => $response_url->toString(),
        '@enquiry_type' => $response_entity_type,
      ]);

      // The owner is the user who this message belongs to.
      if ($account) {
        $message->setOwnerId($account->id());
      }
      $message->save();

      // The e-mail address can be overridden if we don't want
      // to send to the message owner set above.
      $options = [
        'mail' => $contact->getEmail(),
      ];

      $this->getNotifier()->send($message, $options, self::DELIVERY_METHOD);
    }
  }

}
