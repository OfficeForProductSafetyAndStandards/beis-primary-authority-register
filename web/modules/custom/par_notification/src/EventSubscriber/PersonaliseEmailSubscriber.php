<?php

namespace Drupal\par_notification\EventSubscriber;

use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_notification\Event\ParNotificationEvent;
use Drupal\par_notification\Event\ParNotificationEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonaliseEmailSubscriber implements EventSubscriberInterface {

  /**
   * Get the renderer service.
   */
  public function getRenderer() {
    return \Drupal::service('renderer');
  }

  /**
   * React to a message that is about to be sent.
   *
   * @return mixed
   */
  #[\Override]
  static function getSubscribedEvents(): array {
    $events = [];
    if (class_exists(ParDataEvent::class)) {
      $events[ParNotificationEvent::SEND][] = ['onSend', 100];
    }

    return $events;
  }

  /**
   * @param ParNotificationEventInterface $event
   */
  public function onSend(ParNotificationEventInterface $event) {
    $first_name = $event->getRecipient()->getName();
    $output = $event->getOutput();

    $themed_message = [
      '#theme' => 'par_notification',
      '#first_name' => $first_name,
      '#message' => $output['mail_body'],
    ];

    $output['mail_body'] = $this->getRenderer()->renderPlain($themed_message);
    $event->setOutput($output);
  }
}
