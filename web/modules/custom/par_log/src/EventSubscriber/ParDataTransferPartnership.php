<?php

namespace Drupal\par_log\EventSubscriber;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\audit_log\EventSubscriber\EventSubscriberInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Render\Markup;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\Tests\Core\Entity\RevisionableEntity;

/**
 * Responds to audit log events where a partnership name change has occured.
 *
 * @package Drupal\par_log\EventSubscriber
 */
class ParDataTransferPartnership implements EventSubscriberInterface {

  /**
   * The supported action for this logger.
   */
  CONST ACTION = 'update';

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

    $old_name = strtolower((string) $entity->original?->label());
    $new_name = strtolower((string) $entity->label());

    // If the old previous names field value is the same as the current value.
    if ($old_name === $new_name) {
      return FALSE;
    }

    // Set the message format for PAR Entity deletions.
    $message = "The $old_name has been renamed to the $new_name.";
    $event->setMessage($message);

    // If the entity supports PAR statuses.
    if ($entity->hasStatus()) {
      $event->setPreviousState($entity->original->getRawStatus());
      $event->setCurrentState($entity->getRawStatus());
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
