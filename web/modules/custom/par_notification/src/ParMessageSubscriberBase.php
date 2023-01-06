<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_notification\ParMessageHandlerInterface;

/**
 * Provides a base implementation for a Par Link Action plugin.
 *
 * @see \Drupal\par_notification\ParMessageSubscriberInterface
 * @see plugin_api
 */
abstract class ParMessageSubscriberBase extends PluginBase implements ParMessageSubscriberInterface {

  /**
   * The account object.
   *
   * @var AccountInterface
   */
  protected AccountInterface $user;

  /**
   * The account object.
   *
   * @var ParDataManagerInterface
   */
  protected ParDataManagerInterface $par_data_manager;

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->pluginDefinition['status'];
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageType() {
    return $this->pluginDefinition['message'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscriptionRules() {
    return $this->pluginDefinition['rules'];
  }

  /**
   * Get the par message handler.
   *
   * @return ParMessageHandlerInterface
   */
  protected function getMessageHandler(): ParMessageHandlerInterface {
    return \Drupal::service('par_notification.message_handler');
  }

  /**
   * Role-based rules will automatically be processed, provided:
   *  - If the role has the permission to 'bypass par_data membership'
   *    then all users in that role will be recipients.
   *  - If there are no subscribed entities to further filter by
   *    then all users in all authenticated roles will be recipients.
   *
   * User preference-based rules will also automatically be processed,
   * where a user opts-in to receive certain notifications, provided:
   *  - The user belongs to at least one of the subscribed entities.
   *
   * Membership-based rules should be processed by the subscriber plugins
   * and tailored for each message type.
   *
   * {@inheritdoc}
   */
  public function getRecipients(MessageInterface $message): array {
    $recipients = [];

    // User preference-based rules.
    // @todo Discussion needed on whether these are useful:
    //  1) Users rarely known how to update their preferences
    //  2) Saving preferences on the contact record is doesn't
    //     work when we're emailing users who aren't a contact.
    foreach ($this->getSubscribedEntities($message) as $subscribed_entity) {
      if ($subscribed_entity instanceof ParDataMembershipInterface) {
        $people = $subscribed_entity->getPerson();
        foreach ($people as $person) {
          if ($person && $person->hasNotificationPreference($message->getTemplate()->id())) {
            $recipients[] = $person->getEmail();
          }
        }
      }
    }

    return $recipients;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscribedEntities(MessageInterface $message): array {
    return [];
  }

}
