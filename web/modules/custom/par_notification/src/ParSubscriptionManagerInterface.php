<?php

namespace Drupal\par_notification;

use Drupal\user\Entity\Role;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;

/**
 * Defines an interface for the PAR Message Subscriber service.
 *
 * This service will determine who is subscribed to a given message.
 *
 * @see plugin_api
 */
interface ParSubscriptionManagerInterface {

  /**
   * Gets all the recipients of a given message.
   *
   * These email addresses can be obtained from the message, but:
   *  - The email address must have a user account, unless
   *    this notification allows the anonymous role to receive it.
   *  - If there are any subscribed entities the user must belong to them, unless
   *    the user has a role which has the 'bypass par_data membership' permission
   *    and the role is allowed to receive this notification.
   *
   * @param MessageInterface $message
   *   The message that is being sent.
   *
   * @throws ParNotificationException
   *   When no subscribers can be found for the message.
   *
   * @return array
   *   An array of email addresses to send the message to.
   */
  public function getRecipients(MessageInterface $message): array;

  /**
   * Get the PAR entities subscribed to a given message.
   *
   * @param MessageInterface $message
   *   The message being sent.
   *
   * @return ParDataMembershipInterface[]
   *   An array of par data entities that subscribe to this message.
   */
  public function getSubscribedEntities(MessageInterface $message): array;

  /**
   * Get the roles that are subscribed to a given message type.
   *
   * At least one role must be able to receive this message.
   *
   * @throws ParNotificationException
   *   When no roles can be found for the message.
   *
   * @param MessageTemplateInterface $template
   *   The message type that is being sent.
   *
   * @return Role[]
   *   An array of roles that subscribe to this message.
   */
  public function getSubscribedRoles(MessageTemplateInterface $template): array;

}
