<?php

namespace Drupal\par_notification\Plugin\views\field;

use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkManagerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to show which data item the notification refers to.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("par_notification_for")
 */
class ParNotificationFor extends FieldPluginBase {

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
   * @return string
   */
  #[\Override]
  public function render(ResultRow $values) {
    $message = $this->getEntity($values);

    // Only process for message entities.
    if (!$message instanceof MessageInterface) {
      return NULL;
    }

    $notification_types = $this->getLinkManager()->getDefinitionsByNotification($message->getTemplate());
    foreach ($notification_types as $message_definition) {
      $notification_type = $this->getLinkManager()->createInstance($message_definition['id'], []);
      try {
        $primary_field = $notification_type->getPrimaryField();
      }
      catch (\TypeError) {
        continue;
      }
      if ($message->hasField($primary_field)
        && !$message->get($primary_field)->isEmpty()) {
        $primary_entities = $message->get($primary_field)->referencedEntities();
        array_walk($primary_entities, function (&$value) {
          $value = ucfirst((string) $value->label());
        });
        return implode(",", $primary_entities);
      }
    }

    return '';
  }

}
