<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PartnershipApprovedSubscriber implements EventSubscriberInterface {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_revocation_notification
   */
  const MESSAGE_ID = 'partnership_approved_notificatio';

  /**
   * The notification plugin that will deliver these notification messages.
   */
  const DELIVERY_METHOD = 'plain_email';

  protected $recipients = [];

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Nomination event should fire after a partnership has been nominated.
    $events[ParDataEvent::APPROVED][] = ['onPartnershipNomination', -101];

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
  public function onPartnershipNomination(ParDataEventInterface $event) {
    /** @var ParDataEntityInterface $enforcement */
    $partnership = $event->getData();

    // Only act on partnership entities.
    if (!$partnership || $partnership->getEntityTypeId() !== 'par_data_partnership') {
      return;
    }

      // Load the message template.
      $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
      $message_template = $template_storage->load(self::MESSAGE_ID);
      $message_storage = $this->getEntityTypeManager()->getStorage('message');

      // Get the link to approve this notice.
      $options = ['absolute' => TRUE];
      $partnership_url = Url::fromRoute('view.par_user_partnerships.partnerships_page', [], $options);

      if (!$message_template) {
        // @TODO Log that the template couldn't be loaded.
        return;
      }

      // We need to get all the contacts for this partnership.
      $contacts = array_merge($partnership->getAuthorityPeople(), $partnership->getOrganisationPeople());
      if (!$contacts) {
        return;
      }

      foreach ($contacts as $person) {
        // Notify all users in this authority with the appropriate permissions.
        if (($account = $person->getUserAccount())
          && !isset($this->recipients[$account->id()])) {

          // Record the recipient so that we don't send them the message twice.
          $this->recipients[$account->id()] = $account->getEmail();

          // Create one message per user.
          $message = $message_storage->create([
            'template' => $message_template->id()
          ]);

          // Add contextual information to this message.
          if ($message->hasField('field_partnership')) {
            $message->set('field_partnership', $partnership);
          }

          // Add some custom arguments to this message.
          $message->setArguments([
            '@partnership_search_link' => $partnership_url->toString(),
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
