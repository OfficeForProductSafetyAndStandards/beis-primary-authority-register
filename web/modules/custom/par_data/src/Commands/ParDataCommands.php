<?php

namespace Drupal\par_data\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for PAR Data.
 */
class ParDataCommands extends DrushCommands {

  /**
   * The par_data.manager service.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * ParDataCommands constructor.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par_data.manager service.
   */
  public function __construct(ParDataManagerInterface $par_data_manager) {
    parent::__construct();
    $this->parDataManager = $par_data_manager;
  }

  /**
   * Warm the PAR Data caches.
   *
   * @param string $type
   *   The type of data to be warmed.
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
}
