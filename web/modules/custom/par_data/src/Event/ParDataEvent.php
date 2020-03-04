<?php

namespace Drupal\par_data\Event;

use Drupal\Core\Entity\EntityInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The par data event.
 */
class ParDataEvent extends Event implements ParDataEventInterface {

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

  /**
   * The entity being enacted upon.
   *
   * @var ParDataEntityInterface
   */
  protected $entity;

  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * @return ParDataEntityInterface
   */
  public function getEntity() {
    return $this->entity;
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
   */
  public static function statusChange($entity_type_id, $status) {
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
   */
  public static function customAction($entity_type, $action) {
    return implode('.', [self::ENTITY_CUSTOM_ACTION, $entity_type, $action]);
  }

}

