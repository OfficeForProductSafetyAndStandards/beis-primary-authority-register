<?php

namespace Drupal\par_data\Commands;

use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\registered_organisations\OrganisationManagerInterface;
use Drupal\search_api\ConsoleException;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for PAR Data.
 */
class ParDataCommands extends DrushCommands {

  /**
   * The par_data.manager service.
   *
   * @var ParDataManagerInterface
   */
  protected ParDataManagerInterface $parDataManager;

  /**
   * The entity_type.manager service.
   *
   * @var EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The registered_organisations.organisation_manager service.
   *
   * @var OrganisationManagerInterface
   */
  protected OrganisationManagerInterface $organisationManager;

  /**
   * ParDataCommands constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par_data.manager service.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entityTypeManager, OrganisationManagerInterface $organisationManager) {
    parent::__construct();
    $this->parDataManager = $par_data_manager;
    $this->entityTypeManager = $entityTypeManager;
    $this->organisationManager = $organisationManager;
  }

  /**
   * Warm the PAR Data caches.
   *
   * @param string $type
   *   The type of data to be warmed.
   *
   * @validate-module-enabled par_data
   *
   * @command par-data:cache-warm
   * @aliases pcw

   */
  public function cache_warm($type) {
    $count = 0;

    // Can manually choose to warm other caches not listed as defaults.
    if ($type) {
      echo "Warming caches for $type" . PHP_EOL;

      // Warming for selected caches only as it's currently memory intensive.
      foreach ($this->parDataManager->getEntitiesByType($type) as $entity) {
        $count++;
        // By default it's the relationship cache's responsible for entity management we want to warm.
        $entity->getRelationships();

        $unique_function_id = "getRelationships:{$entity->uuid()}:null:null";
        drupal_static_reset($unique_function_id);

        // Assess memory usage.
        if ($count % 100 == 0 && $count > 0) {
          $memory = round(memory_get_usage() / 1024 / 1024, 2);
          $this->output->writeln(dt('@memory MB used in the generation of %count caches', ['@memory' => $memory, '%count' => $count]));
        }
      }

      return "Cache warmed for all $count data entities.";
    }

    return "No caches warmed.";
  }

  /**
   * Check the health of search_api indexes.
   *
   * @param string|null $index
   *   The index id.
   * @param array $options
   *   (optional) An array of options.
   *
   * @throws \Drupal\search_api\ConsoleException
   *   If a batch process could not be created.
   *
   * @validate-module-enabled par_data
   * @validate-module-enabled search_api
   *
   * @command par-data:index-health
   * @option index-health
   *   Whether to check the index health.
   * @aliases pih

   */
  public function index_health($index = NULL, array $options = ['index-health' => NULL]) {
    $include_index_health = $options['index-health'];

    $index_storage = $this->entityTypeManager->getStorage('search_api_index');

    $indexes = $index_storage->loadMultiple([$index]);
    if (!$indexes) {
      return [];
    }

    foreach ($indexes as $index) {
      // Check the health of the server.
      $server_health = $index->isServerEnabled() ? $index->getServerInstance()->hasValidBackend() : FALSE;

      $indexed = $index->getTrackerInstance()->getIndexedItemsCount();
      $total = $index->getTrackerInstance()->getTotalItemsCount();

      $entity_types = $index->getEntityTypes();
      $count = 0;
      foreach ($entity_types as $key => $type) {
        $entity_storage = $this->entityTypeManager->getStorage($type);
        $count += $entity_storage->getQuery()->count()->execute();
      }

      $index_health = (($total == $indexed) && ($indexed == $count));

      if (!$server_health) {
        throw new ConsoleException(dt('Server for index %index is not valid.', ['%index' => $index->id()]));
      }
      if ($include_index_health and !$index_health) {
        throw new ConsoleException(dt('Index %index has only indexed %indexed out of %total items (%count entities).', [
          '%index' => $index->id(),
          '%indexed' => $indexed,
          '%total' => $total,
          '%count' => $count,
        ]));
      }
    }

    return "Index health good.";
  }

  /**
   * Update registered entities.
   *
   * @param ?string $register
   *   The register to update or NULL to update entities not assigned to a register.
   *
   * @validate-module-enabled par_data
   *
   * @command par-data:update-registered-organisations
   * @aliases puro

   */
  public function update_registered_organisations(?string $register = NULL) {
    $count = 0;

    // Check that the register is valid.
    $register_is_valid = $register === ParDataLegalEntity::DEFAULT_REGISTER ??
      (NULL !== $this->organisationManager->getDefinition($register, FALSE));
    $registry = $register_is_valid ? $register : NULL;

    $storage = $this->entityTypeManager
      ->getStorage('par_data_legal_entity');

    $query = $storage->getQuery()
      ->accessCheck(FALSE)
      ->sort('changed' , 'ASC')
      ->range(0, 250);

    if ($register_is_valid) {
      $query->condition('registry', $registry);
    }
    else {
      $query->condition('registry', NULL, 'IS NULL');
    }

    $results = $query->execute();
    /** @var ParDataLegalEntity $entities */
    $entities = $storage->loadMultiple(array_unique($results));

    foreach ($entities as $entity) {
      // Update legacy legal entities.
      $updated = $entity->updateLegacyEntities();

      if ($updated) {
        $entity->save();
        $count++;
        $this->output->writeln(dt('Legacy legal entity @entity updated to the registry %registry', ['@entity' => $entity->label(), '%registry' => $registry]));
      }
    }

    return "$count legacy entities updated.";
  }
}
