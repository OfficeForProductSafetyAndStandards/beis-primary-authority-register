<?php

namespace Drupal\par_notification;
use Drupal\Core\Entity\EntityInterface;
use Drupal\par_notification\Entity\ParMessageInterface;
use Drupal\user\UserInterface;

/**
 * The interface for the message manager.
 */
interface ParMessageManagerInterface {

  /**
   * Build the notification message.
   *
   * @param string $message_id
   * @param UserInterface $recipient
   * @param UserInterface $sender
   * @param EntityInterface $entity
   *
   * @return ParMessageInterface
   *   An array of parameters required for the message.
   */
  public function build($message_id, $recipient, $sender, $entity);

}
