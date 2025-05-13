<?php

namespace Drupal\par_log\EventSubscriber;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\audit_log\EventSubscriber\EventSubscriberInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Responds to audit log events where fields have been removed.
 *
 * @package Drupal\par_log\EventSubscriber
 */
class ParDataRemoveField implements EventSubscriberInterface {

  /**
   * The supported action for this logger.
   */
  const ACTION = 'update';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function reactTo(AuditLogEventInterface $event) {
    if (!$event->getEntity() instanceof ParDataEntityInterface
      || $event->getEventType() != self::ACTION) {
      return FALSE;
    }

    return FALSE;

    /** @var \Drupal\par_data\Entity\ParDataEntityInterface $entity */
    $entity = $event->getEntity();
    $removed = [];
    $revision = [];

    // Analyse all the reference fields for the entity
    // to identify if any fields have been removed.
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity->getEntityTypeId(), $entity->bundle());
    foreach ($field_definitions as $field_name => $definition) {
      if ($definition->getType() === 'entity_reference') {
        $old = $entity->original->retrieveEntitiesKeyedById($field_name);
        $current = $entity->retrieveEntitiesKeyedById($field_name);

        // Any fields that are removed should be logged.
        $diff = array_diff_key($old, $current);
        if (!empty($diff)) {
          foreach ($diff as $e) {
            // PAR Data entities can be converted to string format.
            $removed[] = $e instanceof ParDataEntityInterface ?
              (string) $e :
              "{$e->label()} ({$field_name})";

            // Get the last revision message as the reason.
            if ($entity->getEntityType()->isRevisionable()) {
              $revision_log_field = $entity->getEntityType()->getRevisionMetadataKey('revision_log_message');
              $revision[] = $entity->get($revision_log_field)->getString();
            }
          }
        }
      }
    }

    // Only perform logging for removed fields
    // if there are fields which have been altered.
    if (empty($removed)) {
      return FALSE;
    }

    $label = implode(', ' . PHP_EOL, $removed);
    $parent = (string) $entity;
    $reason = implode(', ' . PHP_EOL, $revision);

    // Set the message format for PAR Entity deletions.
    $message = <<<EOT
The following entities:
$label

Have been removed from:
$parent.

The reason given was:
$reason
EOT;
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
  #[\Override]
  public function getEntityType() {
    return "par_data_entity";
  }

}
