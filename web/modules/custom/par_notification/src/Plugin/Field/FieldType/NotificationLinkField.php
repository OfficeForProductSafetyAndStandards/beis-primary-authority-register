<?php

namespace Drupal\par_notification\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Link;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkManagerInterface;

/**
 * Provides a field type of to display notification links.
 *
 * @FieldType(
 *   id = "par_notification_link_field",
 *   label = @Translation("Notification Status (computed)"),
 *   default_widget = "link_default",
 *   default_formatter = "link",
 *   constraints = {"LinkType" = {}, "LinkAccess" = {}, "LinkExternalProtocols" = {}, "LinkNotExistingInternal" = {}}
 * )
 */
class NotificationLinkField extends FieldItemList implements FieldItemListInterface {

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
  * {@inheritdoc}
  */
  protected function computeValue() {
    $message = $this->getEntity();

    // Only process for message entities.
    if ($message instanceof MessageInterface) {
      $link = $this->getLinkManager()->link($message);
    }

    // Add links to values array.
    if ($link instanceof Link) {
      $this->list[0] = $this->createItem(0, $link);
    }
  }

}
