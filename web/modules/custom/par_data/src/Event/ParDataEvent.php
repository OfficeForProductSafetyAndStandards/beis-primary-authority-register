<?php

namespace Drupal\par_data\Event;

use Drupal\par_data\Entity\ParDataEntityInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The par data event.
 */
class ParDataEvent extends Event implements ParDataEventInterface {

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

  /**
   * The name of the event triggered when an existing par entity is approved.
   *
   * @Event
   *
   * @var string
   */
  const APPROVED = 'par_data.entity.approve';

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
