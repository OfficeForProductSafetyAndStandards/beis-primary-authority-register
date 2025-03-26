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
  protected string $listName;

  /**
   * Email.
   *
   * @var string
   */
  protected string $email;

  /**
   * The subscription entity.
   *
   * @var \Drupal\par_subscriptions\Entity\ParSubscriptionInterface
   */
  protected ParSubscriptionInterface $subscription;

  /**
   * The entity object.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected readonly EntityInterface $entity;

  /**
   * React to the updating of the PAR subscription information.
   *
   * @param \Drupal\par_subscriptions\Entity\ParSubscriptionInterface $subscription
   *   The PAR subscription.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
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
  #[\Override]
  public function getEntity(): EntityInterface {
    return $this->entity;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getListName(): string {
    return $this->listName;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getSubscription(): ParSubscriptionInterface {
    return $this->subscription;
  }

}
