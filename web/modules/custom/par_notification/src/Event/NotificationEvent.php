<?php

namespace Drupal\par_notification\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * The notification.sent event is dispatched each time a notification is created
 * in the system.
 */
class NotificationEvent extends Event {

  const NAME = 'order.placed';

  protected $order;

  public function __construct(Order $order) {
    $this->order = $order;
  }

  public function getOrder() {
    return $this->order;
  }
}
