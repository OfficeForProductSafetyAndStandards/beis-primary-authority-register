<?php

namespace Drupal\par_data\Event;

use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * The event is dispatched each time a PAR entity is acted upon.
 */
interface ParDataEventInterface {

  /**
   * @return ParDataEntityInterface
   */
  public function getData();

}
