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
  #[\Override]
  protected function computeValue() {
    $message = $this->getEntity();

    // Only process for message entities.
    if ($message instanceof MessageInterface) {
      // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
      // Please confirm that `$` is an instance of `\Drupal\Core\Entity\EntityInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
      $link = $this->getLinkManager()->toLink($message)->toString();

      // Add links to values array.
      if ($link instanceof Link) {
        $this->list[0] = $this->createItem(0, $link);
      }
    }

    return NULL;
  }

}
