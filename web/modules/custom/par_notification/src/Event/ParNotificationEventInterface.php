<?php

namespace Drupal\par_notification\Event;

use Drupal\message\MessageInterface;

/**
 * The event is dispatched each time a message is sent.
 */
interface ParNotificationEventInterface {

  /**
   * Get the message that is being sent.
   *
   * @return MessageInterface
   *   The message.
   */
  public function getMessage(): MessageInterface;

  /**
   * Get the rendered output.
   *
   * @return array
   *   Return the altered message output,
   *   this must be an array with the keys:
   *    - mail_subject
   *    - mail_body
   */
  public function getOutput(): array;

  /**
   * Set the rendered output.
   *
   * @param array $output
   *   The message output must have the following view mode keys:
   *    - mail_subject
   *    - mail_body
   */
  public function setOutput(array $output);

}
