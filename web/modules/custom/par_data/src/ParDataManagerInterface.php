<?php

namespace Drupal\par_data;

use Drupal\Core\Entity\EntityTypeInterface;

/**
* A controller for all styleguide page output.
*/
interface ParDataManagerInterface {

  /**
  * Gets a list of all entity types for PAR Data.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
  */
  public function getParEntityTypes();

  /**
   * Gets the entity definition for the class that defines an entities bundles.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   */
  public function getEntityTypeDefinition(EntityTypeInterface $definition);

}
