<?php

namespace Drupal\par_subscriptions\Event;

/**
 * Defines subscription events.
 */
final class SubscriptionEvents {

  /**
   * Fired after a new subscription is added.
   *
   * @Event
   *
   * @var string
   */
  const string SUBSCRIBE = 'par_subscriptions.subscribe';

  /**
   * Fired after a subscription is removed.
   *
   * @Event
   *
   * @var string
   */
  const string UNSUBSCRIBE = 'par_subscriptions.unsubscribe';

  /**
   * Returns the event name when subscribing to a specific list.
   *
   * @param string $list
   *   The list ID.
   *
   * @return string
   *   The event name.
   */
  public static function subscribe($list) {
    return self::SUBSCRIBE . '.' . $list;
  }

  /**
   * Returns the event name when unsubscribing to a specific list.
   *
   * @param string $list
   *   The list ID.
   *
   * @return string
   *   The event name.
   */
  public static function unsubscribe($list) {
    return self::UNSUBSCRIBE . '.' . $list;
  }

}
