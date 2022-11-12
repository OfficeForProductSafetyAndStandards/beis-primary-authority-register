<?php

namespace Drupal\par_notification;

use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\message_notify\MessageNotifier;
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
   * @var ParDataEventInterface $event
   */
  protected ParDataEventInterface $event;

  /**
   * @var array $recipients
   */
  protected array $recipients = [];

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel(): string {
    return 'par';
  }

  /**
   * Get the par link manager.
   *
   * @return ParLinkManager
   */
  public function getLinkManager(): ParLinkManager {
    return \Drupal::service('plugin.manager.par_link_manager');
  }

  /**
   * Get the entity type manager.
   *
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return \Drupal::entityTypeManager();
  }

  /**
   * Get the notification service.
   *
   * @return MessageNotifier
   */
  public function getNotifier(): MessageNotifier {
    return \Drupal::service('message_notify.sender');
  }

  /**
   * Get the current user sending the message.
   *
   * @return AccountProxyInterface
   */
  public function getCurrentUser(): AccountProxyInterface {
    return \Drupal::currentUser();
  }

  /**
   * Get message template storage.
   *
   * @return EntityStorageInterface
   *   The entity storage for message template (bundle) entities.
   */
  public function getMessageTemplateStorage(): EntityStorageInterface {
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
   * @param string $message_template_id
   *   The message template id.
   *
   * @return MessageTemplateInterface|void
   */
  public function getMessageTemplate($message_template_id = NULL): ?MessageTemplateInterface {
    if (!$message_template_id) {
      $message_template_id = static::MESSAGE_ID;
    }

    // Check that a template could be found.
    /** @var MessageTemplateInterface $message_template */
    $message_template = $this->getMessageTemplateStorage()->load($message_template_id);

    if ($message_template instanceof MessageTemplateInterface) {
      return $message_template;
    }
    else {
      $this->getLogger($this->getLoggerChannel())->warning('Could not find the message template for %message.', ['%message' => $message_template_id]);
    }
  }

  /**
   * Create the message.
   *
   * @throws ParNotificationException
   */
  public function createMessage() {
    // Create one message per user.
    $template_id = $this->getMessageTemplate()?->id();
    if (!$template_id) {
      throw new ParNotificationException('No template could be found.');
    }

    return $this->getMessageStorage()->create([
      'template' => $template_id,
    ]);
  }

  /**
   * Send the message.
   */
  public function sendMessage(MessageInterface $message, $email) {
    // The message needs to record who it is sent to in cases where it
    // is not sent to a user with an account.
    // PAR-1734: Invitations can be generated from this information.
    if ($message->hasField('field_to')) {
      $message->set('field_to', $email);
    }

    // Get all the primary tasks for this notification.
    $tasks = $this->getLinkManager()->retrieveTasks($message->getTemplate());
    // If any of the tasks have been completed then throw a message error.
    foreach ($tasks as $id => $task) {
      if ($task->isComplete($message)) {
        $this->getLogger($this->getLoggerChannel())->warning('The message \'%message_template\' does not need to be sent because the primary action has already been completed .', ['%message_template' => $message->getTemplate()->label()]);
        return;
      }
    }

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
