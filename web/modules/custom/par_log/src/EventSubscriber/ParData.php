<?php

namespace Drupal\par_log\EventSubscriber;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\audit_log\EventSubscriber\EventSubscriberInterface;
use Drupal\Core\Render\Markup;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Processes par data entity events.
 *
 * @package Drupal\par_log\EventSubscriber
 */
class ParData implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public function reactTo(AuditLogEventInterface $event) {
    $entity = $event->getEntity();
    if (!$entity instanceof ParDataEntityInterface) {
      return FALSE;
    }

    $event_type = $event->getEventType();

    /** @var \Drupal\par_data\Entity\ParDataEntityInterface $entity */
    $current_state = $entity->isPublished() ? 'published' : 'unpublished';
    $previous_state = '';
    if (isset($entity->original)) {
      $previous_state = $entity->original->isPublished() ? 'published' : 'unpublished';
    }
    $args = [
      '@title' => Markup::create($entity->label()),
    ];

    if ($event_type == 'delete') {
      $event
        ->setMessage(t('@title was deleted.', $args))
        ->setPreviousState($previous_state)
        ->setCurrentState(NULL);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType() {
    return 'par_data_entity';
  }

}
