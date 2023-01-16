<?php

namespace Drupal\par_notification;

use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\message\MessageTemplateInterface;
use Drupal\par_data\Event\ParDataEventInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\message_notify\MessageNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ParEventSubscriberBase implements EventSubscriberInterface {

  use LoggerChannelTrait;

  /**
   * The message template ID created for this notification.
   */
  const MESSAGE_ID = '';

  /**
   * @var ParDataEventInterface $event
   */
  protected ParDataEventInterface $event;

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel(): string {
    return 'par';
  }

  /**
   * Get the current user sending the message.
   *
   * @return AccountProxyInterface
   */
  public function getCurrentUser(): AccountProxyInterface {
    return \Drupal::currentUser();
  }

  /**
   * Get the PAR message handler.
   *
   * @return ParMessageHandlerInterface
   *   The message handler.
   */
  public function getMessageHandler(): ParMessageHandlerInterface {
    return \Drupal::service('par_notification.message_handler');
  }

  /**
   * Getter for the event.
   */
  public function getEvent() {
    return $this->event;
  }

  /**
   * Setter for the event.
   */
  public function setEvent($event) {
    $this->event = $event;
  }

}
