<?php

namespace Drupal\par_data\Event;

/**
 * The event is dispatched each time a PAR entity is acted upon.
 */
interface ParDataEventInterface {

  /**
   * PAR Data get entity.
   *
   * @return \Drupal\par_data\Entity\ParDataEntityInterface
   *   Performs getEntity function.
   */
  public function getEntity();

}
