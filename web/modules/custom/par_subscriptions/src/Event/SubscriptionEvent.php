<?php

namespace Drupal\par_subscriptions\Event;

use Drupal\Core\Entity\EntityInterface;
use Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent;
use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;

/**
 * The event fired for crud operations on subscription entities.
 *
 * @package Drupal\par_subscriptions\Event
 */
class SubscriptionEvent extends EntityUpdateEvent implements SubscriptionEventInterface {

  /**
   * The list name.
   *
   * @var string
   */
  protected $listName;

  /**
   * Email.
   *
   * @var string
   */
  protected $email;

  /**
   * The subscription entity.
   *
   * @var \Drupal\par_subscriptions\Entity\ParSubscriptionInterface
   */
  protected $subscription;

  /**
   * Handles a subscription event.
   *
   * @param \Drupal\par_subscriptions\Entity\ParSubscriptionInterface $subscription
   *   The subscription entity.
   */
  public function __construct(ParSubscriptionInterface $subscription) {
    $this->listName = $subscription->getListName();
    $this->email = $subscription->getEmail();
    $this->subscription = $subscription;

    parent::__construct($subscription);
  }

  /**
   * Returns the email address.
   *
   * @return string
   *   The email Address.
   */
  #[\Override]
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * Returns the subscription list name.
   *
   * @return string
   *   The subscription list name
   */
  #[\Override]
  public function getListName(): string {
    return $this->listName;
  }

  /**
   * Returns the subscription entity.
   *
   * @return \Drupal\par_subscriptions\Entity\ParSubscriptionInterface
   *   Return the subscription.
   */
  #[\Override]
  public function getSubscription(): ParSubscriptionInterface {
    return $this->subscription;
  }

}
