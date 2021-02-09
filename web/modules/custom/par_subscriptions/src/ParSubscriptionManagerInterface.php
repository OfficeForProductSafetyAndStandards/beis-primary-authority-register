<?php

namespace Drupal\par_subscriptions;

/**
 * Interface for the Par Subscription Manager.
 */
interface ParSubscriptionManagerInterface {

  /**
   * Get all available lists.
   *
   * @return array
   *   An array containing the available list ids.
   */
  public function getLists();
}
