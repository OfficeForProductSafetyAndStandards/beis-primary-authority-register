<?php

namespace Drupal\par_subscriptions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;

use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;
use Drupal\user\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 */
class UserDefaultSubscriber implements EventSubscriberInterface {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events[EntityInsertEvent::class][] = ['onEvent', 10];
    return $events;
  }

  /**
   *
   */
  public function getSubscriptionManager() {
    return \Drupal::service('par_subscriptions.manager');
  }

  /**
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   */
  public function onEvent(EntityInsertEvent $event) {
    if ($event->getEntity() instanceof UserInterface) {
      /** @var \Drupal\user\Entity\User $user */
      $user = $event->getEntity();

      $lists = $this->getSubscriptionManager()->getLists();

      foreach ($lists as $list) {
        $subscription = $user->id() > 1 ?
          $this->getSubscriptionManager()->createSubscription($list, $user->getEmail()) :
          NULL;

        // Silently subscribe & verify the user.
        if ($subscription instanceof ParSubscriptionInterface) {
          $subscription->verify();
        }
      }
    }
  }

}
