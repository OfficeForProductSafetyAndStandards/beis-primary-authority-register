<?php

namespace Drupal\par_notification\Plugin\ParNotifier;

use Drupal\Core\Entity\EntityInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_notification\Entity\ParMessageInterface;
use Drupal\user\UserInterface;

/**
 * The interface for the par notifier plugins.
 */
interface ParNotifierPluginInterface {

  /**
   * Deliver notifications.
   *
   * @param ParDataPersonInterface $recipient
   * @param ParMessageInterface $entity
   *
   * @return bool
   *   TRUE if a notification could be delivered.
   */
  public function deliver($recipient, $entity);

}
