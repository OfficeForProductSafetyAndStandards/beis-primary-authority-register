<?php

namespace Drupal\par_data;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\par_data\Entity\ParDataTypeInterface;

/**
* Interface for the Par Data Manager.
*/
interface ParDataManagerInterface {

  /**
   * Gets a list of all entity types for PAR Data.
   *
   * @return EntityTypeInterface[]
  */
  public function getParEntityTypes(): array;

  /**
   * Get a given PAR Data Entity Type
   *
   * @param string $type
   *
   * @return EntityTypeInterface|null
   *   A PAR Data Entity Type
   */
  public function  getParEntityType(string $type): ?EntityTypeInterface;

  /**
   * An entity query builder.
   *
   *
   * @param string $type
   *   An entity type to query.
   * @param array $conditions
   *   Array of Conditions.
   * @param integer $limit
   *   Limit number of results.
   * @param string $sort
   *   The field to sort by.
   * @param string $direction
   *   The direction to sort in.
   *
   * @return EntityInterface[]
   *   An array of entity objects indexed by their IDs. Returns an empty array
   *   if no matching entities are found.
   */
  public function getEntitiesByQuery(string $type, array $conditions, $limit = NULL, $sort = NULL, $direction = 'ASC'): array;

  /**
   * Gets the entity definition for the class that defines an entities bundles.
   *
   * @param EntityTypeInterface $definition
   *
   * @return EntityTypeInterface|null
   */
  public function getEntityBundleDefinition(EntityTypeInterface $definition): ?EntityTypeInterface;

  /**
   * Get the config entity that provides bundles for a given entity.
   *
   * @param string $type
   *   The type of entity to get the bundle entity for.
   * @param mixed $bundle
   *   The bundle to load if there are multiple for a given
   *
   * @return ParDataTypeInterface
   *   A PAR Data Bundle Entity
   */
  public function getParBundleEntity(string $type, mixed $bundle = NULL): ?ParDataTypeInterface;

  /**
   * @param string $definition
   *   The entity type to get the storage for
   *
   * @return NULL|EntityStorageInterface
   *   The entity storage for the given definition.
   */
  public function getEntityTypeStorage(string $definition): ?EntityStorageInterface;

  /**
   * @param string $type
   *   The type of entity to get the bundle entity for.
   * @param string $field
   *   The field name to retrieve the definition for.
   * @param mixed $bundle
   *   The bundle to load if there are multiple for a given.
   *
   * @return NULL|FieldDefinitionInterface
   *   The field definition.
   */
  public function getFieldDefinition(string $type, string $field, mixed $bundle = NULL): ?FieldDefinitionInterface;

}
