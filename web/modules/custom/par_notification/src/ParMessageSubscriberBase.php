<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\message\MessageInterface;
use Drupal\par_data\ParDataManagerInterface;

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
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $user;

  /**
   * The account object.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
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
   * @return \Drupal\par_notification\ParMessageHandlerInterface
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
   * Membership-based rules should be processed by the subscriber plugins
   * and tailored for each message type.
   *
   * {@inheritdoc}
   */
  #[\Override]
  public function getRecipients(MessageInterface $message): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getSubscribedEntities(MessageInterface $message): array {
    return [];
  }

}
