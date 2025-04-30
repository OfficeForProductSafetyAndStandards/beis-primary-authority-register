<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\message\MessageInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\message_expire\MessageExpiryManagerInterface;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Base class for PAR event subscribers.
 */
abstract class ParEventSubscriberBase implements EventSubscriberInterface {

  use LoggerChannelTrait;

  /**
   * The message template ID created for this notification.
   */
  const MESSAGE_ID = '';

  /**
   * The event object.
   *
   * @var \Drupal\par_data\Event\ParDataEventInterface|null
   */
  protected ?ParDataEventInterface $event = NULL;

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * Constructs a ParEventSubscriberBase object.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service.
   */

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   The logger channel to use.
   */
  public function getLoggerChannel(): string {
    return 'par';
  }

  /**
   * Gets the current user sending the message.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   The current user.
   */
  public function getCurrentUser(): AccountProxyInterface {
    return \Drupal::currentUser();
  }

  /**
   * Gets the PAR message handler.
   *
   * @return \Drupal\par_notification\ParMessageHandlerInterface
   *   The message handler.
   */
  public function getMessageHandler(): ParMessageHandlerInterface {
    return \Drupal::service('par_notification.message_handler');
  }

  /**
   * Gets the message expiry service.
   *
   * @return \Drupal\message_expire\MessageExpiryManagerInterface
   *   The message expiry service.
   */
  public function getMessageExpiryService(): MessageExpiryManagerInterface {
    return \Drupal::service('message_expire.manager');
  }

  /**
   * Gets the event.
   *
   * @return \Drupal\par_data\Event\ParDataEventInterface|null
   *   The event object.
   */
  public function getEvent():?ParDataEventInterface {
    return $this->event;
  }

  /**
   * Sets the event.
   *
   * @param \Drupal\par_data\Event\ParDataEventInterface|null $event
   *   The event object.
   */
  public function setEvent(?ParDataEventInterface $event): void {
    $this->event = $event;
  }

  /**
   * Gets the messages associated with this event.
   *
   * @param \Drupal\par_data\Event\ParDataEventInterface $event
   *   The event object.
   *
   * @return array
   *   An array of messages.
   */
  public function getMessages(ParDataEventInterface $event): array {
    $entity = $event->getEntity();

    $template = $this->getMessageHandler()->getMessageTemplateStorage()->load(static::MESSAGE_ID);
    $field = $this->getMessageHandler()->getPrimaryField($template);

    $messages = '';
    if ($field && $template instanceof MessageTemplateInterface && $entity instanceof EntityInterface) {
      $messages = $this->getMessageHandler()->getMessageStorage()
        ->loadByProperties([
          'template' => $template->id(),
          $field => $entity->id(),
        ]);

      // Sort the messages in descending order with the most recent first.
      krsort($messages);
    }

    return $messages;
  }

  /**
   * Sends and saves the message.
   *
   * @param array $arguments
   *   An array of replacement arguments to be set on the message.
   * @param array $parameters
   *   The additional data parameters to be added to the message.
   *     - key is a field name
   *     - value is the value to set that field to.
   */
  public function sendMessage(array $arguments, array $parameters = []) {
    $entity = $this->getEvent()?->getEntity();

    // Create the message.
    try {
      $message = $this->getMessageHandler()->createMessage(static::MESSAGE_ID);
    }
    catch (ParNotificationException $e) {
      $this->getLogger('PAR')
        ->error('Failed to create message: @error', ['@error' => $e->getMessage()]);
      return;
    }

    if ($entity instanceof EntityInterface && $message instanceof MessageInterface) {

      $field = $this->getMessageHandler()->getPrimaryField($message->getTemplate());
      // Add the primary contextual information to this message.
      if ($field && $message->hasField($field)) {
        $message->set($field, $entity);
      }
      // Add any additional parameters to this message.
      foreach ($parameters as $parameter_field => $parameter) {
        if ($message->hasField($parameter_field)) {
          $message->set($parameter_field, $parameter);
        }
        else {
          $this->getLogger('PAR')
            ->warning('Parameter field "@field" does not exist on message template.', ['@field' => $parameter_field]);
        }
      }

      // Add some custom arguments to this message.
      $arguments = array_merge($message->getArguments(), $arguments);
      $message->setArguments($arguments);

      // Save the message (this will also send it).
      try {
        $message->save();
      }
      catch (\Exception $e) {
        $this->getLogger('PAR')
          ->error('Failed to save message: @error', ['@error' => $e->getMessage()]);
      }
    }
  }

}
