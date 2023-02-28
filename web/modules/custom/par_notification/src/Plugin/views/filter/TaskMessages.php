<?php

namespace Drupal\par_notification\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Task message views filter, excludes messages that are not tasks.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("task_messages")
 */
class TaskMessages extends FilterPluginBase {

  public function query() {// Ensure the base table.
    $this->ensureMyTable();

    // Get the data table alias.
    $data_table = \Drupal::entityTypeManager()->getStorage('message')->getDataTable();
    $this->query->ensureTable($data_table, $this->relationship);
    $alias = $this->query->ensureTable($data_table, $this->relationship);

    $entity_type_manager = \Drupal::entityTypeManager();
    $par_link_manager = \Drupal::service('plugin.manager.par_link_manager');

    // Get the bundle key.
    $entity_type = $entity_type_manager->getDefinition('message');
    $bundle_key = $entity_type->getKey('bundle');

    // Get the template IDs for messages that have tasks.
    $task_templates = $par_link_manager->getTaskTemplates();
    array_walk($task_templates, function (&$value) {
      $value = $value->id();
    });

    if (!empty($task_templates)) {
      $condition = ($this->query->getConnection()->condition('AND'))
        ->condition("{$alias}.{$bundle_key}", $task_templates, 'IN');

      $this->query->addWhere($this->options['group'], $condition);
    }
  }
}
