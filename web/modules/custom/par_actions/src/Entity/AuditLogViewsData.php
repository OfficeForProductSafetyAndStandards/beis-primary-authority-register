<?php

namespace Drupal\audit_log\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Audit log entities.
 */
class AuditLogViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['audit_log']['target_entity_view_link'] = [
      'title' => $this->t('Target entity view link'),
      'real field' => 'entity_id',
      'field' => [
        'id' => 'audit_log_target_view_link',
      ],
    ];

    $data['audit_log']['event']['filter'] = [
      'id' => 'in_operator',
      'options callback' => 'Drupal\audit_log\Entity\AuditLogViewsData::getEventOptions',
    ];

    $data['audit_log']['entity_type']['filter'] = [
      'id' => 'in_operator',
      'options callback' => 'Drupal\audit_log\Entity\AuditLogViewsData::getEntityTypeOptions',
    ];

    $data['audit_log']['created']['filter']['id'] = 'audit_log_date';

    return $data;
  }

  /**
   * Get event options.
   */
  public static function getEventOptions() {
    return [
      'insert' => t('Insert'),
      'update' => t('Update'),
      'delete' => t('Delete'),
    ];
  }

  /**
   * Get entity type options.
   */
  public static function getEntityTypeOptions() {
    $entity_manager = \Drupal::entityTypeManager();
    $subscribers = \Drupal::service('audit_log.logger')->getEventSubscribers();
    $return = [];

    /* @var \Drupal\audit_log\EventSubscriber\EventSubscriberInterface[] $subscribers */
    foreach ($subscribers as $subscriber) {
      $entity_type = $subscriber->getEntityType();
      if ($entity_manager->hasDefinition($entity_type)) {
        $definition = $entity_manager->getDefinition($entity_type);
        $return[$entity_type] = $definition->getLabel();
      }
    }

    return $return;
  }

}
