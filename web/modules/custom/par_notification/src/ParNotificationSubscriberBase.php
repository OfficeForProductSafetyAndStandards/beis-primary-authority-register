<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ParNotificationSubscriberBase implements EventSubscriberInterface {

  use LoggerChannelTrait;

  /**
   * The notication plugin that will deliver these notification messages.
   */
  const DELIVERY_METHOD = 'plain_email';

  /**
   * The message template ID created for this notification.
   */
  const MESSAGE_ID = '';

  /**
   * @var $event
   */
  protected $event;

  /**
   * @var array $recipients
   */
  protected $recipients = [];

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
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
   * Get message template storage.
   */
  public function getMessageTemplateStorage() {
    return $this->getEntityTypeManager()->getStorage('message_template');
  }

  /**
   * Get message template storage.
   */
  public function getMessageStorage() {
    return $this->getEntityTypeManager()->getStorage('message');
  }

  /**
   * Getter for the event.
   */
  public function getEvent() {
    return $this->event;
  }

  /**
   * Setter for the event.
   */
  public function setEvent($event) {
    $this->event = $event;
  }

  /**
   * Getter for the message template id.
   *
   * Checks whether the template is valid.
   *
   * @param string $message_id
   *   The message template id.
   *
   * @return string|NULL
   */
  public function getMessageTemplateId($message_id = NULL) {
    if (!$message_id) {
      $message_id = static::MESSAGE_ID;
    }

    // Load the message template.
    if ($message_template = $this->getMessageTemplateStorage()->load($message_id)) {
      return $message_id;
    }
    else {
      $this->getLogger($this->getLoggerChannel())->warning('Could not find the message template for %message.', ['%message' => $message_id]);
    }
  }

  /**
   * Create the message.
   */
  public function createMessage() {
    // Create one message per user.
    return $this->getMessageStorage()->create([
      'template' => $this->getMessageTemplateId(),
    ]);
  }

  /**
   * Send the message.
   */
  public function sendMessage(MessageInterface $message, $email) {
    if ($message->save()) {
      // Choose who to send the notification to.
      $options = [
        'mail' => $email,
      ];

      $this->getNotifier()->send($message, $options, static::DELIVERY_METHOD);
    }
    else {
      $this->getLogger($this->getLoggerChannel())->warning('Could not save the \'%message_template\' message for %email.', ['%email' => $email, '%message_template' => $message->getTemplate()->label()]);
    }
  }

}
