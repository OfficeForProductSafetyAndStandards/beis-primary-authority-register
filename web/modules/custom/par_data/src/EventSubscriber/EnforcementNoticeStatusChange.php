<?php

namespace Drupal\par_data\EventSubscriber;

use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\Event\ParDataEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnforcementNoticeStatusChange implements EventSubscriberInterface {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  static function getSubscribedEvents() {
    // React to Enforcement Actions being reviewed.
    $events[ParDataEvent::statusChange('par_data_enforcement_action', 'approved')][] = ['onNoticeStatusChange', 900];
    $events[ParDataEvent::statusChange('par_data_enforcement_action', 'blocked')][] = ['onNoticeStatusChange', 900];
    $events[ParDataEvent::statusChange('par_data_enforcement_action', 'referred')][] = ['onNoticeStatusChange', 900];

    return $events;
  }

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager() {
    return \Drupal::entityTypeManager();
  }

  /**
   * Get the current user sending the message.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   */
  public function getCurrentUser() {
    return \Drupal::currentUser();
  }

  /**
   * Listens for status changes to Enforcement Actions
   * and fires a status change for the notice when all
   * actions are reviewed.
   *
   * @param ParDataEventInterface $event
   */
  public function onNoticeStatusChange(ParDataEvent $event) {
    /** @var ParDataEntityInterface $par_data_enforcement_action */
    $par_data_enforcement_action = $event->getEntity();

    // Get all the sibling actions associated with the parent enforcement notice.
    $par_data_enforcement_notice = $par_data_enforcement_action->getEnforcementNotice(TRUE);
    $primary_action = $par_data_enforcement_notice ?
      $par_data_enforcement_notice->getEnforcementActions(True) :
      $par_data_enforcement_action;

    // Only trigger an enforcement notice status change on the first updated item.
    if ($par_data_enforcement_action->id() !== $primary_action->id()) {
      return;
    }

    // Dispatch the status update event for enforcement notices.
    $event = new ParDataEvent($par_data_enforcement_notice);
    $event_to_dispatch = $event->statusChange($par_data_enforcement_notice->getEntityTypeId(), 'reviewed');
    $dispatcher = \Drupal::service('event_dispatcher');
    $dispatcher->dispatch($event, $event_to_dispatch);
  }

}
