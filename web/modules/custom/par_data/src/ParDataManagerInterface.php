<?php

namespace Drupal\par_data;

use Drupal\Core\Entity\EntityTypeInterface;

/**
* Interface for the Par Data Manager.
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
   * @param \Drupal\Core\Entity\EntityTypeInterface
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   */
  public function getEntityBundleDefinition(EntityTypeInterface $definition);

  /**
   * @return NULL|\Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage for the given definition.
   */
  public function getEntityTypeStorage(EntityTypeInterface $definition);

}
