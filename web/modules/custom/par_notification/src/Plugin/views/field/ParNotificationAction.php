<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\par_notification\Plugin\views\field;

use Drupal\Component\Utility\Html;
use Drupal\Core\Link;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkManagerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to show the status of any actions for the notification.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_notification_action")
 */
class ParNotificationAction extends FieldPluginBase {

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
   *
   * @param ResultRow $values
   *
   * @return string
   */
  public function render(ResultRow $values) {
    $message = $this->getEntity($values);

    // Only process for message entities.
    if (!$message instanceof MessageInterface) {
      return NULL;
    }

    $tasks = $this->getLinkManager()->retrieveTasks($message->getTemplate());
    if (!empty($tasks) && $this->getLinkManager()->isComplete($message) === FALSE) {
      // Display a red tag for tasks with action required.
      $colour = 'govuk-tag--red';
      $status = 'Action required';
    }
    else if (!empty($tasks)) {
      // Display a blue tag for tasks that have been completed.
      $colour = 'govuk-tag--blue';
      $status = 'Complete';
    }
    else {
      // Display a grey tag for notifications that don't require action.
      $colour = 'govuk-tag--grey';
      $status = 'No action needed';
    }

    $status_tag = [
      '#type' => 'html_tag',
      '#tag' => 'strong',
      '#value' => $status,
      '#attributes' => ['class' => ['govuk-tag', $colour]],
    ];

    return $this->getRenderer()->render($status_tag);
  }
}
