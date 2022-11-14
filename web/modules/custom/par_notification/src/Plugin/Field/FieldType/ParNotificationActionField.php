<?php

namespace Drupal\par_notification\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Link;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\Core\Render\RendererInterface;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkManagerInterface;

/**
 * Provides a field type of baz.
 *
 * @FieldType(
 *   id = "par_notification_action_field",
 *   label = @Translation("PAR Notification Action Required"),
 *   default_widget = "boolean_checkbox",
 *   default_formatter = "boolean",
 * )
 */
class ParNotificationActionField extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * Get the PAR Link Manager service.
   *
   * @return ParLinkManagerInterface
   */
  public function getLinkManager(): ParLinkManagerInterface {
    return \Drupal::service('plugin.manager.par_link_manager');
  }

  /**
   * Get the Drupal renderer service.
   *
   * @return RendererInterface
   */
  public function getRenderer(): RendererInterface {
    return \Drupal::service('renderer');
  }

  /**
  * {@inheritdoc}
  */
  protected function computeValue() {
    /** @var \Drupal\message\MessageInterface $message */
    $message = $this->getEntity();

    // Only process for message entities.
    if (!$message instanceof MessageInterface) {
      return NULL;
    }

    $tasks = $this->getLinkManager()->retrieveTasks($message);

    foreach ($tasks as $plugin_id => $task) {
      if (!$task->isComplete()) {
        // Set the field value as needs completion.
        $this->appendItem(TRUE);
        return;
      }
    }

    // No actions required.
    $this->appendItem(FALSE);
  }

}
