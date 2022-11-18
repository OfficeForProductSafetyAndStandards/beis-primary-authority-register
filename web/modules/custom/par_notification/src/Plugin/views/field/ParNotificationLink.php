<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\par_notification\Plugin\views\field;

use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkManagerInterface;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\Boolean;

/**
 * Field handler to show whether action is required for a given PAR notification.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_notification_action")
 */
class ParNotificationAction extends Boolean {

  /**
   * Get the PAR Link Manager service.
   *
   * @return ParLinkManagerInterface
   */
  public function getLinkManager(): ParLinkManagerInterface {
    return \Drupal::service('plugin.manager.par_link_manager');
  }

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * @{inheritdoc}
   */
  public function getValue(ResultRow $values, $field = NULL) {
    /** @var MessageInterface $entity */
    $entity = $this->getEntity($values);

    // Only process for message entities.
    if (!$entity instanceof MessageInterface) {
      return NULL;
    }

    $tasks = $this->getLinkManager()->retrieveTasks($entity->getTemplate());

    // No action required by default.
    $value = FALSE;
    foreach ($tasks as $plugin_id => $task) {
      if (!$task->isComplete($entity)) {
        // Set the action to be required.
        $value = TRUE;
        break;
      }
    }

    return $value;
  }
}
