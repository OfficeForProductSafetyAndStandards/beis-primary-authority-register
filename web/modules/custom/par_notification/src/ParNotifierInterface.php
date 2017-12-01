<?php

namespace Drupal\par_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\par_notification\Entity\ParMessageInterface;
use Drupal\user\UserInterface;

/**
 * The interface for the par notifier plugin manager.
 */
interface ParNotifierInterface {

  /**
   * Deliver notification.
   *
   * @param ParDataPersonInterface $recipient
   * @param string $message_id
   * @param string $plugin_id
   * @param EntityInterface $entity
   */
  public function notify($recipient, $message_id, $plugin_id, $entity);

}
