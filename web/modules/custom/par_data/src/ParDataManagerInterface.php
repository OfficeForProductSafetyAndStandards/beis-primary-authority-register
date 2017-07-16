<?php

namespace Drupal\par_data;


/**
* A controller for all styleguide page output.
*/
interface ParDataManagerInterface {

  /**
  * The main index page for the styleguide.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
  */
  public function getParEntityTypes();

}
