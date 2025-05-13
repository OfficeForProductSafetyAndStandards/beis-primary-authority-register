<?php

namespace Drupal\par_notification;

use Drupal\message\MessageInterface;

/**
 * Defines an interface for the Par Link Action plugins.
 *
 * @see plugin_api
 */
interface ParTaskInterface {

  /**
   * Returns whether a task has been completed.
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message interface that needs redirection.
   *
   * @Throws ParNotificationException
   *
   * @return bool
   *   Whether the task has been completed.
   */
  public function isComplete(MessageInterface $message): bool;

}
