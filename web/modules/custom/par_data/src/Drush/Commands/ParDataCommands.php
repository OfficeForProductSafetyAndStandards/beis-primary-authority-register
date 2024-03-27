<?php

namespace Drupal\par_data\Drush\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\registered_organisations\OrganisationManagerInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Drush commandfile.
 */
final class ParDataCommands extends DrushCommands {

  /**
   * Constructs a ParDataCommands object.
   */
  public function __construct(
    private readonly ParDataManagerInterface $parDataManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly OrganisationManagerInterface $organisationManager,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_data.manager'),
      $container->get('entity_type.manager'),
      $container->get('registered_organisations.organisation_manager'),
    );
  }

  /**
   * Warm the PAR Data caches.
   */
  #[CLI\Command(name: 'par_data:cache-warm', aliases: ['pcw'])]
  #[CLI\Argument(name: 'type', description: 'The type of data to be warmed.')]
  #[CLI\Usage(name: 'par_data:cache-warm par_data_authority', description: 'Usage description')]
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
   * @throws \Drupal\search_api\ConsoleException
   *   If a batch process could not be created.
   */
   #[CLI\Command(name: 'par_data:index-health', aliases: ['pih'])]
   #[CLI\Argument(name: 'index', description: 'The type of data to be warmed.')]
   #[CLI\Option(name: 'index-health', description: 'Whether to check the index health.')]
   #[CLI\Usage(name: 'par_data:index-health partnerships', description: 'Usage description')]
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
        $count += $entity_storage->getQuery()->accessCheck()->count()->execute();
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
   */
   #[CLI\Command(name: 'par_data:update-registered-organisations', aliases: ['puro'])]
   #[CLI\Argument(name: 'register', description: 'The register to update or NULL to update entities not assigned to a register.')]
   #[CLI\Usage(name: 'par_data:update-registered-organisations companies_house', description: 'Usage description')]
  public function update_registered_organisations(?string $register = NULL) {
    $scheduler = \Drupal::service('plugin.manager.par_scheduler');

    try {
      $definitions = $scheduler->getDefinitions();
      $plugin_definition = $definitions['legal_entity_registry_convert'] ?? NULL;
      if ($plugin_definition) {
        $plugin = $scheduler->createInstance($plugin_definition['id'], $plugin_definition);
        $plugin->run();
      }
    }
    catch (PluginNotFoundException $e) {
      return "Failed to convert legacy legal entities.";
    }

    return "All legacy entities updated for processing.";
  }

}
