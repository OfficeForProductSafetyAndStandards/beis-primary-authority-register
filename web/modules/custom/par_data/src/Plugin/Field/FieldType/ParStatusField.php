<?php

namespace Drupal\par_data\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Provides a field type of baz.
 *
 * @FieldType(
 *   id = "par_status_field",
 *   label = @Translation("PAR Status"),
 *   default_formatter = "par_list_formatter",
 *   default_widget = "string_textfield",
 * )
 */
class ParStatusField extends FieldItemList {

  use ComputedItemListTrait;

  /**
  * {@inheritdoc}
  */
  protected function computeValue() {
    $entity = $this->getEntity();

    if ($entity instanceof ParDataEntityInterface) {
      // Get the status field.
      $status_field = $entity->getTypeEntity()?->getConfigurationElementByType('entity', 'status_field');

      // The default status for all entities is 'active'.
      $status = ParDataEntity::STATUS_DEFAULT;

      // Check to see if the entity has a status field.
      if ($status_field && $entity->hasField($status_field)) {
        $status = $entity->get($status_field)->getString();
      }

      // Check if the entity is revoked.
      if ($entity->getTypeEntity()->isRevokable() && $entity->isRevoked()) {
        $status = ParDataEntity::REVOKE_FIELD;
      }

      // Check if the entity is archived.
      if ($entity->getTypeEntity()->isArchivable() && $entity->isArchived()) {
        $status = ParDataEntity::ARCHIVE_FIELD;
      }
    }

    // Add the status value.
    $this->list[] = $this->createItem(0, $status ?? NULL);
  }

}
