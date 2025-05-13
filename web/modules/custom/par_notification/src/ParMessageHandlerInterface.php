<?php

namespace Drupal\par_notification;

use Drupal\message\MessageInterface;

/**
 * Defines an interface for the par message handler.
 *
 * @see plugin_api
 */
interface ParMessageHandlerInterface {

  /**
   * Create a new message.
   *
   * @return \Drupal\message\MessageInterface
   *   The newly created message.
   */
  public function createMessage(string $template_id);

  /**
   * Send a message.
   *
   * This is fired by whenever a new message is saved,
   *
   * @see par_notification_entity_presave()
   * @see par_notification_entity_insert()
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message to send.
   */
  public function sendMessage(MessageInterface $message);

  /**
   * Return the primary data associated with a message.
   *
   * This should be the data which relates best to this message,
   * the data that triggered the message to be sent.
   *
   * There are some messages which aren't triggered by any specific
   * data but more generally by an event or user interaction.
   * These are not expected to return any primary data.
   *
   * @throws ParNotificationException
   *   Throws an exception if data is expected and is missing.
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message to get data for.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An ar
   */
  public function getPrimaryData(MessageInterface $message): array;

  /**
   * Get the thread for a given message, if the message supports it.
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message to get the thread for.
   *
   * @return string
   *   An optional thread ID if the message supports grouping.
   *   Otherwise an individual message id will be returned.
   */
  public function getThreadId(MessageInterface $message): string;

}
