<?php

namespace Drupal\par_notification\Event;

use Drupal\message\MessageInterface;
use Drupal\par_notification\ParRecipient;
use Drupal\par_notification\ParSubscriptionManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

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
   * The message being acted upon.
   *
   * @param \Drupal\message\MessageInterface $message
   */
  protected MessageInterface $message;

  /**
   * The email address that the message is being sent to.
   *
   * @param \Drupal\par_notification\ParRecipient $recipient
   */
  protected ParRecipient $recipient;

  /**
   * The rendered output of the message being sent.
   *
   * There should be an array key for each view_mode, including:
   *  - mail_subject
   *  - mail_body.
   *
   * @param array $output
   */
  protected array $output;

  public function __construct(MessageInterface $message, string $email, array $output) {
    $this->message = $message;
    $this->output = $output;

    // Get all the recipients for this message.
    $recipients = $this->getSubscriptionManager()->getRecipients($message);

    // Filter for just this recipient.
    $recipients = array_filter($recipients, function ($recipient) use ($email) {
      return $recipient->getEmail() === $email;
    });
    $this->recipient = current($recipients);
  }

  /**
   * Get the subscription manager.
   *
   * @return \Drupal\par_notification\ParSubscriptionManagerInterface
   */
  public function getSubscriptionManager(): ParSubscriptionManagerInterface {
    return \Drupal::service('plugin.manager.par_subscription_manager');
  }

  /**
   * @return \Drupal\message\MessageInterface
   */
  public function getMessage(): MessageInterface {
    return $this->message;
  }

  /**
   * @return \Drupal\par_notification\ParRecipient
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
  public function setOutput(array $output) {
    if (isset($output['mail_subject']) && isset($output['mail_body'])) {
      $this->output = $output;
    }
  }

}
