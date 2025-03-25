<?php

namespace Drupal\par_notification\Event;

use Drupal\message\MessageInterface;
use Drupal\par_notification\ParRecipient;
use Symfony\Contracts\EventDispatcher\Event;
use Drupal\par_notification\ParSubscriptionManagerInterface;

/**
 * The event fired whenever a notification is being sent out.
 */
class ParNotificationEvent extends Event implements ParNotificationEventInterface {

  /**
   * The name of the event triggered when a notification is sent.
   *
   * @Event
   *
   * @var string
   */
  const SEND = 'par_notification.send';

  /**
   * The email address that the message is being sent to.
   *
   * @param ParRecipient $recipient
   */
  protected ParRecipient $recipient;

  public function __construct(/**
   * The message being acted upon.
   *
   * @param MessageInterface $message
   */
  protected MessageInterface $message, string $email, /**
   * The rendered output of the message being sent.
   *
   * There should be an array key for each view_mode, including:
   *  - mail_subject
   *  - mail_body
   *
   * @param array $output
   */
  protected array $output) {
    // Get all the recipients for this message.
    $recipients = $this->getSubscriptionManager()->getRecipients($this->message);

    // Filter for just this recipient
    $recipients = array_filter($recipients, fn($recipient) => $recipient->getEmail() === $email);
    $this->recipient = current($recipients);
  }

  /**
   * Get the subscription manager.
   *
   * @return ParSubscriptionManagerInterface
   */
  public function getSubscriptionManager(): ParSubscriptionManagerInterface {
    return \Drupal::service('plugin.manager.par_subscription_manager');
  }

  /**
   * @return MessageInterface
   */
  #[\Override]
  public function getMessage(): MessageInterface {
    return $this->message;
  }

  /**
   * @return ParRecipient
   */
  public function getRecipient(): ParRecipient {
    return $this->recipient;
  }

  /**
   * Get the message output.
   *
   * This will be an array with the keys:
   *   - 'mail_subject'
   *   - 'mail_body'
   *
   * @return array
   */
  #[\Override]
  public function getOutput(): array {
    return $this->output;
  }

  /**
   * Update the message output.
   *
   * This must be an array with the keys:
   *   - 'mail_subject'
   *   - 'mail_body'
   */
  #[\Override]
  public function setOutput(array $output) {
    if (isset($output['mail_subject']) && isset($output['mail_body'])) {
      $this->output = $output;
    }
  }

}

