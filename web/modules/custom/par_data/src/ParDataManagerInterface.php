<?php

namespace Drupal\par_data;

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
   */
  public function getParEntityTypes(): array;

  /**
   * Get a given PAR Data Entity Type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   *   A PAR Data Entity Type
   */
  public function getParEntityType(string $type): ?EntityTypeInterface;

  /**
   * An entity query builder.
   *
   * @param string $type
   *   An entity type to query.
   * @param array $conditions
   *   Array of Conditions.
   * @param int $limit
   *   Limit number of results.
   * @param string $sort
   *   The field to sort by.
   * @param string $direction
   *   The direction to sort in.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entity objects indexed by their IDs. Returns an empty array
   *   if no matching entities are found.
   */
  public function getEntitiesByQuery(string $type, array $conditions, $limit = NULL, $sort = NULL, $direction = 'ASC'): array;

  /**
   * Gets the entity definition for the class that defines an entities bundles.
   */
  public function getEntityBundleDefinition(EntityTypeInterface $definition): ?EntityTypeInterface;

  /**
   * Get the config entity that provides bundles for a given entity.
   *
   * @param string $type
   *   The type of entity to get the bundle entity for.
   * @param mixed $bundle
   *   The bundle to load if there are multiple for a given.
   *
   * @return \Drupal\par_data\Entity\ParDataTypeInterface
   *   A PAR Data Bundle Entity
   */
  public function getParBundleEntity(string $type, $bundle = NULL): ?ParDataTypeInterface;

  /**
   * Get the Entity storage.
   *
   * @param string $definition
   *   The entity type to get the storage for.
   *
   * @return null|EntityStorageInterface
   *   The entity storage for the given definition.
   */
  public function getEntityTypeStorage(string $definition): ?EntityStorageInterface;

  /**
   * Get the field definitions.
   *
   * @param string $type
   *   The type of entity to get the bundle entity for.
   * @param string $field
   *   The field name to retrieve the definition for.
   * @param mixed $bundle
   *   The bundle to load if there are multiple for a given.
   *
   * @return null|FieldDefinitionInterface
   *   The field definition.
   */
  public function getFieldDefinition(string $type, string $field, $bundle = NULL): ?FieldDefinitionInterface;

}
