<?php

namespace Drupal\par_data\Event;

/**
 * The event is dispatched each time a PAR entity is acted upon.
 */
interface ParDataEventInterface {

  /**
   * @return \Drupal\par_data\Entity\ParDataEntityInterface
   */
  public function getEntity();

}
