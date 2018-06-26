<?php

namespace Drupal\par_data_test\EventSubscriber;

use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Subscriber MyEventSubscriber.
 */
class ImportSubscriber implements EventSubscriberInterface {

  /**
   * We need to re-save all user entities to link them to the par_person records.
   */
  public function onRespond(ImportEvent $event) {
    $entities = $event->getImportedEntities();
    foreach ($entities as $entity) {
      if ($entity->getEntityTypeId() === 'user') {
        $entity->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[DefaultContentEvents::IMPORT][] = ['onRespond', -100];
    return $events;
  }

}
