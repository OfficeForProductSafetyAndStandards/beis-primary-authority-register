<?php

namespace Drupal\par_notification\Plugin\views\field;

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
   * @return \Drupal\par_notification\ParLinkManagerInterface
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
   * @param \Drupal\views\ResultRow $values
   *
   * @return \Drupal\Component\Render\MarkupInterface|null
   *
   * @throws \Exception
   */
  #[\Override]
  public function render(ResultRow $values) {
    $message = $this->getEntity($values);

    // Only process for message entities.
    if (!$message instanceof MessageInterface) {
      return NULL;
    }

    $link = $this->getLinkManager()->link($message);
    if ($link instanceof Link &&
        $link->getUrl()->access() &&
        $link->getUrl()->isRouted()) {
      $render_array = $link->toRenderable();
    }

    return !empty($render_array) ?
      $this->getRenderer()->render($render_array) : NULL;
  }

}
