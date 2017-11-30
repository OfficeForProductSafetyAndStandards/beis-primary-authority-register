<?php

namespace Drupal\par_data\Event;

use Drupal\par_data\Entity\ParDataEntityInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The notification.sent event is dispatched each time a notification is created
 * in the system.
 */
class ParDataEvent extends Event {

  /**
   * The name of the event triggered when a new par entity is created.
   *
   * @Event
   *
   * @var string
   */
  const CREATE = 'par_data.entity.create';

  /**
   * The name of the event triggered when an existing par entity is updated.
   *
   * @Event
   *
   * @var string
   */
  const UPDATE = 'par_data.entity.update';

  /**
   * The name of the event triggered when an existing par entity is deleted.
   *
   * @Event
   *
   * @var string
   */
  const DELETE = 'par_data.entity.delete';

  protected $data;

  public function __construct(ParDataEntityInterface $data) {
    $this->data = $data;
  }

  /**
   * @return ParDataEntityInterface
   */
  public function getData() {
    return $this->data;
  }

}
