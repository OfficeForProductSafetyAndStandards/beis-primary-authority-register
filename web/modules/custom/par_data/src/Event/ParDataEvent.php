<?php

namespace Drupal\par_data\Event;

use Drupal\Core\Entity\EntityEvent;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * The event fired for crud operations on PAR Data entities.
 */
class ParDataEvent extends EntityEvent implements ParDataEventInterface {

  /**
   * The name of the event triggered when an existing par entity is updated.
   *
   * @Event
   *
   * @var string
   */
  const STATUS_CHANGE = 'par_data.entity.status';


  /**
   * The name of the event triggered when entity reference is actioned.
   *
   * @Event
   *
   * @var string
   */
  const ENTITY_CUSTOM_ACTION = 'par_data.entity.custom_action';

  public function __construct(ParDataEntityInterface $entity) {
    parent::__construct($entity);
  }

  /**
   * Generate the entity status event name for the currently saved entity.
   *
   * @param string $entity_type_id
   *   The entity type for the event.
   * @param string $status
   *   The status changed to.
   *
   * @return string
   *   The string being returned.
   */
  public static function statusChange(string $entity_type_id, string $status): string {
    return implode('.', [self::STATUS_CHANGE, $entity_type_id, $status]);
  }

  /**
   * Generate the custom entity action event name for the given entity.
   *
   * @param string $entity_type
   *   The entity type for the event.
   * @param string $action
   *   The custom action being performed.
   *
   * @return string
   *   The string being returned.
   */
  public static function customAction(string $entity_type, string $action): string {
    return implode('.', [self::ENTITY_CUSTOM_ACTION, $entity_type, $action]);
  }

}
