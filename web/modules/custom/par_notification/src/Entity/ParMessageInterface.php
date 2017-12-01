<?php

namespace Drupal\par_notification\Entity;

use Drupal\Core\Link;

/**
 * The interface for all Flow Entities.
 */
interface ParMessageInterface {

  /**
   * Get the subject or heading.
   *
   * @return string
   *   The subject for this message.
   */
  public function getSubject();

  /**
   * Get the message body.
   *
   * @return string
   *   The message body.
   */
  public function getMessage();

  /**
   * Set the subject or heading.
   *
   * @param string $subject
   *
   * @return string
   *   The subject for this message.
   */
  public function setSubject($subject);

  /**
   * Set the message body.
   *
   * @param string $message
   *
   * @return string
   *   The message body.
   */
  public function setMessage($message);

}
