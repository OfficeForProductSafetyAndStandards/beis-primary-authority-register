<?php

namespace Drupal\par_subscriptions\Event;

use Drupal\Core\Entity\EntityEvent;
use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;

/**
 * The event fired for crud operations on subscription entities.
 *
 * @package Drupal\par_subscriptions\Event
 */
class SubscriptionEvent extends EntityEvent implements SubscriptionEventInterface {

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
   * @param \Drupal\par_subscriptions\Entity\ParSubscriptionInterface $subscription
   */
  public function __construct(ParSubscriptionInterface $subscription) {
    $this->listName = $subscription->getListName();
    $this->email = $subscription->getEmail();
    $this->subscription = $subscription;

    parent::__construct($subscription);
  }

  /**
   * @return string
   */
  public function getEmail(){
    return $this->email;
  }

  /**
   * @return string
   */
  public function getListName(){
    return $this->listName;
  }

  /**
   * @return \Drupal\par_subscriptions\Entity\ParSubscriptionInterface
   */
  public function getSubscription(){
    return $this->subscription;
  }

}
