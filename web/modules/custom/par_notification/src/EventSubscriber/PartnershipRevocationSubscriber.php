<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PartnershipRevocationSubscriber implements EventSubscriberInterface {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/partnership_revocation_notificat
   */
  const MESSAGE_ID = 'partnership_revocation_notificat';

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
    // Revocation event should fire after most default events to make sure
    // revocation has not been cancelled.
    $events[ParDataEvent::statusChange('par_data_partnership', 'revoked')][] = ['onPartnershipRevocation', -100];

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
  public function onPartnershipRevocation(ParDataEventInterface $event) {
    /** @var ParDataEntityInterface $par_data_partnership */
    $par_data_partnership = $event->getEntity();

    // Load the message template.
    $template_storage = $this->getEntityTypeManager()->getStorage('message_template');
    $message_template = $template_storage->load(self::MESSAGE_ID);

    $message_storage = $this->getEntityTypeManager()->getStorage('message');
    if (!$message_template) {
      // @TODO Log that the template couldn't be loaded.
      return;
    }

    // We need to get all the contacts for this partnership.
    $contacts = array_merge($par_data_partnership->getAuthorityPeople(), $par_data_partnership->getOrganisationPeople());
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
          $message->set('field_partnership', $par_data_partnership);
        }

        // Add some custom arguments to this message.
        $message->setArguments([
          '@partnership_authority' => $par_data_partnership->getAuthority(TRUE)->label(),
          '@partnership_organisation' => $par_data_partnership->getOrganisation(TRUE)->label(),
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
