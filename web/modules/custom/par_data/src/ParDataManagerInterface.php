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
   * Get a given PAR Data Entity Type
   *
   * @param string $type
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   *   A PAR Data Entity Type
   */
  public function getParEntityType(string $type);

  /**
   * @param string $type
   *   An entity type to query.
   * @param array $conditions
   *   Array of Conditions.
   * @param integer $limit
   *   Limit number of results.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   */
  public function getEntitiesByQuery(string $type, array $conditions, $limit = NULL);

  /**
   * Gets the entity definition for the class that defines an entities bundles.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   */
  public function getEntityBundleDefinition(EntityTypeInterface $definition);

  /**
   * Get the config entity that provides bundles for a given entity.
   *
   * @param string $type
   *   The type of entity to get the bundle entity for.
   * @param mixed $bundle
   *   The bundle to load if there are multiple for a given
   *
   * @return \Drupal\par_data\Entity\ParDataTypeInterface
   *   A PAR Data Bundle Entity
   */
  public function getParBundleEntity(string $type, $bundle = NULL);

  /**
   * @param string $definition
   *   The entity type to get the storage for
   *
   * @return NULL|\Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage for the given definition.
   */
  public function getEntityTypeStorage($definition);

  /**
   * Get the entities related to each other.
   *
   * Follows some rules to make sure it doesn't go round in loops.
   *
   * @param $entity
   *   The entity being looked up.
   * @param array $entities
   *   The entities relationship tree.
   * @param array $processedEntities
   *   The hash table of entities that have already been processed, includes those ignored in the entity relationship tree.
   * @param int $iteration
   *   The depth of relationships to go before stopping.
   * @param bool $force_lookup
   *   Force the lookup of relationships that would otherwise be ignored.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An array of entities keyed by entity type.
   */
  public function getRelatedEntities($entity, $entities = [], &$processedEntities = [], $iteration = 0, $force_lookup = FALSE);

}
