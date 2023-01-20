<?php

namespace Drupal\par_notification\Event;

use Drupal\message\MessageInterface;
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
   * @param MessageInterface $message
   */
  protected MessageInterface $message;

  /**
   * The email address that the message is being sent to.
   *
   * @param string $recipient
   */
  protected string $recipient;

  /**
   * The rendered output of the message being sent.
   *
   * There should be an array key for each view_mode, including:
   *  - mail_subject
   *  - mail_body
   *
   * @param array $output
   */
  protected array $output;

  public function __construct(MessageInterface $message, string $recipient, array $output) {
    $this->message = $message;
    $this->recipient = $recipient;
    $this->output = $output;
  }

  /**
   * {@inheritDoc}
   */
  public function getMessage(): MessageInterface {
    return $this->message;
  }

  /**
   * {@inheritDoc}
   */
  public function getRecipient(): string {
    return $this->recipient;
  }

  /**
   * {@inheritDoc}
   */
  public function getOutput(): array {
    return $this->output;
  }

  /**
   * {@inheritDoc}
   */
  public function setOutput(array $output) {
    if (isset($output['mail_subject']) && isset($output['mail_body'])) {
      $this->output = $output;
    }
  }

}

