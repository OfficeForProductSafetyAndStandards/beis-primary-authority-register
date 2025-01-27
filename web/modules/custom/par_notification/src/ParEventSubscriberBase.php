<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\message\MessageInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\message_expire\MessageExpiryManagerInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ParEventSubscriberBase implements EventSubscriberInterface {

  use LoggerChannelTrait;

  /**
   * The message template ID created for this notification.
   */
  const MESSAGE_ID = '';

  /**
   * @var ParDataEventInterface $event
   */
  protected ParDataEventInterface $event;

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
   * Get the current user sending the message.
   *
   * @return AccountProxyInterface
   */
  public function getCurrentUser(): AccountProxyInterface {
    return \Drupal::currentUser();
  }

  /**
   * Get the PAR message handler.
   *
   * @return ParMessageHandlerInterface
   *   The message handler.
   */
  public function getMessageHandler(): ParMessageHandlerInterface {
    return \Drupal::service('par_notification.message_handler');
  }

  /**
   * Get the message expiry service.
   *
   * @return MessageExpiryManagerInterface
   *   The message expiry service.
   */
  public function getMessageExpiryService(): MessageExpiryManagerInterface {
    return \Drupal::service('message_expire.manager');
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
   * Get the messages associated with this event.
   */
  public function getMessages($event) {
    $entity = $event?->getEntity();

    /** @var MessageTemplateInterface $template */
    $template = $this->getMessageHandler()->getMessageTemplateStorage()->load(static::MESSAGE_ID);
    $field = $this->getMessageHandler()->getPrimaryField($template);

    $messages = [];
    if ($field && $template instanceof MessageTemplateInterface) {
      $messages = $this->getMessageHandler()->getMessageStorage()
        ->loadByProperties([
          'template' => $template?->id(),
          $field => $entity->id(),
        ]);

      // Sort the messages in descending order with the most recent first.
      ksort($messages);
    }

    return $messages;
  }

  /**
   * Send and save the message.
   *
   * @param array $arguments
   *   An array of replacement arguments to be set on the message.
   * @param ParDataEntityInterface[] $parameters
   *   The additional data parameters to be added to the message.
   */
  public function sendMessage(array $arguments = [], array $parameters = []) {
    $entity = $this->getEvent()?->getEntity();

    // Create the message.
    try {
      $message = $this->getMessageHandler()->createMessage(static::MESSAGE_ID);
    } catch (ParNotificationException $e) {
      return;
    }

    if ($entity instanceof EntityInterface &&
      $message instanceof MessageInterface) {

      $field = $this->getMessageHandler()->getPrimaryField($message->getTemplate());
      // Add the primary contextual information to this message.
      if ($field && $message->hasField($field)) {
        $message->set($field, $entity);
      }
      // Add any additional parameters to this message.
      foreach ($parameters as $parameter_field => $parameter) {
        if ($field && $message->hasField($parameter_field)) {
          $message->set($parameter_field, $parameter);
        }
      }

      // Add some custom arguments to this message.
      $arguments = array_merge($message->getArguments(), $arguments);
      $message->setArguments($arguments);

      // Save the message (this will also send it).
      $message->save();
    }
  }

}
