<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\message\MessageInterface;

/**
 * Defines an interface for the Par Message Subscriber plugins.
 *
 * @see plugin_api
 */
interface ParMessageSubscriberInterface extends PluginInspectionInterface {

  /**
   * Gets the email addresses that a message will be sent to.
   *
   * There are some rules which are applied automatically in the plugin base.
   *
   * @see ParMessageSubscriberBase
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message that is being sent.
   *
   * @return string[]
   *   An array of email addresses to send the message to.
   */
  public function getRecipients(MessageInterface $message): array;

  /**
   * Get the PAR entities subscribed to a given message,
   * this determines who will be able to view the message
   *
   * Only entities with ParDataSubscriptionInterface can subscribe to messages:
   *  - Authorities
   *  - Organisations.
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message that is being sent.
   *
   * @return \Drupal\par_data\Entity\ParDataMembershipInterface[]
   *   An array of par data entities that subscribe to this message.
   */
  public function getSubscribedEntities(MessageInterface $message): array;

}
