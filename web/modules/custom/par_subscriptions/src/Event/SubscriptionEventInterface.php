<?php

namespace Drupal\par_subscriptions\Event;

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
   * @return \Drupal\par_subscriptions\Entity\ParSubscriptionInterface
   */
  public function getSubscription();

  /**
   * @return \Drupal\par_subscriptions\Entity\ParSubscriptionInterface
   */
  public function getEntity();

}
