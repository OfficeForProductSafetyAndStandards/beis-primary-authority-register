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
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Render\RendererInterface;

/**
 * Field handler to show the action link for the given notification.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_notification_summary")
 */
class ParNotificationSummary extends FieldPluginBase {

  /**
   * Get the message entity view builder.
   *
   * @return EntityViewBuilderInterface
   */
  public function getViewBuilder(): EntityViewBuilderInterface {
    return \Drupal::entityTypeManager()->getViewBuilder('message');
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

    $display = $this->getViewBuilder()->view($message, 'summary');

    return !empty($display) ?
      $this->getRenderer()->render($display) : NULL;
  }
}
