<?php

namespace Drupal\par_subscriptions\Event;

use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;

/**
 * Interface for the Subscription Event.
 */
interface SubscriptionEventInterface {

  /**
   * Get the email address.
   *
   * @return string
   */
  public function getEmail();

  /**
   * Get the human readable list name.
   *
   * @return string
   */
  public function getListName();

  /**
   * Get the subscription entity.
   *
   * @return ParSubscriptionInterface
   */
  public function getSubscription();

  /**
   * @return ParSubscriptionInterface
   */
  public function getEntity();
}
