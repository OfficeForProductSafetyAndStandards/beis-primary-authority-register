<?php

namespace Drupal\par_log\EventSubscriber;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\audit_log\EventSubscriber\EventSubscriberInterface;
use Drupal\Core\Render\Markup;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Responds to audit log events where entities are deleted.
 *
 * @package Drupal\par_log\EventSubscriber
 */
class ParDataDelete implements EventSubscriberInterface {

  /**
   * The supported action for this logger.
   */
  CONST ACTION = 'delete';

  /**
   * {@inheritdoc}
   */
  public function reactTo(AuditLogEventInterface $event) {
    if (!$event->getEntity() instanceof ParDataEntityInterface
      || $event->getEventType() != self::ACTION) {
      return FALSE;
    }

    /** @var \Drupal\par_data\Entity\ParDataEntityInterface $entity */
    $entity = $event->getEntity();

    $reason = !$entity->{ParDataEntity::DELETE_REASON_FIELD}->isEmpty() ?
      (string) $entity->getPlain(ParDataEntity::DELETE_REASON_FIELD) :
      '';
    $label = (string) $entity;

    // Set the message format for PAR Entity deletions.
    $message = <<<EOT
$label was deleted.

The reason given was:
$reason
EOT;
    $event->setMessage($message);

    // If the entity supports PAR statuses.
    if ($entity->hasStatus()) {
      $event->setPreviousState($entity->getRawStatus());
      $event->setCurrentState(NULL);
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType() {
    return "par_data_entity";
  }

}
