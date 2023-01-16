<?php

namespace Drupal\par_subscriptions\EventSubscriber;

use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParEventSubscriberBase;
use Drupal\par_subscriptions\Event\SubscriptionEventInterface;
use Drupal\par_subscriptions\Event\SubscriptionEvents;
use Drupal\par_subscriptions\ParSubscriptionManager;
use Drupal\user\Entity\User;

class VerifySubscriber extends ParEventSubscriberBase {

  /**
   * The message template ID created for this notification.
   *
   * @see /admin/structure/message/manage/subscription_verification_notification
   */
  const MESSAGE_ID = 'subscription_verify_notification';

  const LIST = 'par_news';

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // Confirmation event should fire after a subscription has been saved.
    $events[SubscriptionEvents::subscribe(self::LIST)][] = ['onEvent', -101];

    return $events;
  }

  /**
   * @param \Drupal\par_subscriptions\Event\SubscriptionEventInterface $event
   */
  public function onEvent(SubscriptionEventInterface $event) {
    // Automatically verify signed in users.
    if ($this->getCurrentUser()->isAuthenticated()) {
      $account = User::load($this->getCurrentUser()->id());
      if ($account->getEmail() === $event->getEmail()) {
        // Silently verify any user subscribing themselves.
        $event->getSubscription()->verify();
      }
    }

    // Do not send a verification message if this subscription is already verified.
    if ($event->getSubscription()->isVerified()) {
      return;
    }

    try {
      /** @var Message $message */
      $message = $this->createMessage();
    }
    catch (ParNotificationException $e) {
      $this->getLogger('par')->warning('The subscription verification message could not be sent to @recipient', ['@recipient' => $event->getEmail()]);
    }

    // Generate the verification link.
    $parameters = ['subscription_code' => $event->getSubscription()->getCode()];
    $options = ['absolute' => true];
    $verification_link = Url::fromRoute("par_subscriptions.{$event->getSubscription()->getListId()}.verify", $parameters, $options);

    // Add some custom arguments to this message.
    $message->setArguments([
      '@list' => $event->getListName(),
      '@verification_link' => $verification_link->toString(),
      '@subscription_code' => $event->getSubscription()->getCode(),
    ]);

    // Send the message.
    $this->sendMessage($message, $event->getEmail());
  }
}
