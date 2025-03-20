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
 * Field handler to show the action link for the given notification.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_notification_link")
 */
class ParNotificationLink extends FieldPluginBase {

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
  #[\Override]
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
  #[\Override]
  public function render(ResultRow $values) {
    $message = $this->getEntity($values);

    // Only process for message entities.
    if (!$message instanceof MessageInterface) {
      return NULL;
    }

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Please confirm that `$` is an instance of `\Drupal\Core\Entity\EntityInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
    $link = $this->getLinkManager()->toLink($message)->toString();
    if ($link instanceof Link &&
        $link->getUrl()->access() &&
        $link->getUrl()->isRouted()) {
      $render_array = $link->toRenderable();
    }

    return !empty($render_array) ?
      $this->getRenderer()->render($render_array) : NULL;
  }
}
