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
   * @param ParDataEventInterface $event
   */
  public function onNewReply(EntityEvent $event) {
    /** @var CommentInterface $comment */
    $comment = $event->getEntity();

    // Load the message template.
    $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
    $message_template = $template_storage->load(self::MESSAGE_ID);

    $message_storage = $this->getEntityTypeManager()->getStorage('message');

    // Get the response entity type to customize the message,
    // without the 'par' prefix.
    $par_data_entity = $comment->getCommentedEntity();
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

    // Notify the primary authority contact and the enforcing officer contact for this response.
    $contacts = [];
    if ($par_data_entity && $par_data_entity instanceof ParDataEntityInterface) {
      if ($authority_person = $par_data_entity->getPrimaryAuthorityContact()) {
        $authority_person_account = $authority_person->lookupUserAccount();
        if ($authority_person_account
          && $authority_person_account->hasPermission($permission)
          && $authority_person_account->id() !== $comment->getOwnerId()) {
          $contacts[$authority_person_account->id()] = $authority_person_account;
        }
      }
      if ($enforcing_person = $par_data_entity->getEnforcingPerson(TRUE)) {
        $enforcing_person_account = $enforcing_person->lookupUserAccount();
        if ($enforcing_person_account
          && $enforcing_person_account->hasPermission($permission)
          && $enforcing_person_account->id() !== $comment->getOwnerId()) {
          $contacts[$enforcing_person_account->id()] = $enforcing_person_account;
        }
      }
    }

    foreach ($contacts as $account) {
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
        'mail' => $account->getEmail(),
      ];

      $this->getNotifier()->send($message, $options, self::DELIVERY_METHOD);
    }
  }

}
