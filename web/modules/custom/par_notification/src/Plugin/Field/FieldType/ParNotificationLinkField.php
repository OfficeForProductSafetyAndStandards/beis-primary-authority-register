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
 *   id = "par_notification_link_field",
 *   label = @Translation("PAR Notification Link"),
 *   default_widget = "link_default",
 *   default_formatter = "link",
 *   constraints = {"LinkType" = {}, "LinkAccess" = {}, "LinkNotExistingInternal" = {}}
 * )
 */
class ParNotificationLinkField extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * Get the PAR Link Manager service.
   *
   * @return ParLinkManagerInterface
   */
  public function getLinkManager(): ParLinkManagerInterface {
    return \Drupal::service('par_link.manager');
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

    $primary_action = $this->getLinkManager()->link($message);

    // Add the link to the field values.
    if ($primary_action instanceof Link) {
      $this->appendItem($primary_action);
    }
  }

}
