<?php

namespace Drupal\par_notification\Plugin\views\field;

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
   * @return \Drupal\par_notification\ParLinkManagerInterface
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
   * @param \Drupal\views\ResultRow $values
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
    if (!empty($tasks) &&
      ($this->getLinkManager()->isComplete($message) === FALSE)) {
      $link_value = $this->getLinkManager()->link($message)->toString();
      if (strip_tags($link_value) != 'Approve the notification of enforcement action') {
        // Display a red tag for tasks with action required.
        $colour = 'govuk-tag--red';
        $status = 'Action required';
      }
      else {
        $colour = 'govuk-tag--grey';
        $status = 'No action needed';
      }
    }
    elseif (!empty($tasks)) {
      // Display a blue tag for tasks that have been completed.
      $colour = '';
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
