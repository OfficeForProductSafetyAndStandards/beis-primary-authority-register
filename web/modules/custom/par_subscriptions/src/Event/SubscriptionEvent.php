<?php

namespace Drupal\par_subscriptions\Event;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Event\EventBase;
use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;

/**
 * The event fired for crud operations on subscription entities.
 *
 * @package Drupal\par_subscriptions\Event
 */
class SubscriptionEvent extends EventBase implements SubscriptionEventInterface {

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
   * The entity object.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * @param \Drupal\par_subscriptions\Entity\ParSubscriptionInterface $subscription
   * @param \Drupal\Core\Entity\EntityInterface $entity
   */
  public function __construct(
    ParSubscriptionInterface $subscription,
    EntityInterface $entity,
  ) {
    $this->listName = $subscription->getListName();
    $this->email = $subscription->getEmail();
    $this->subscription = $subscription;
    $this->entity = $entity;

    parent::__construct($subscription);
  }

  /**
   * Returns the entity wrapped by this event.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The entity object.
   */
  public function getEntity() {
    return $this->entity;
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
