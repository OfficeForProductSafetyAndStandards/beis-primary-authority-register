<?php

namespace Drupal\par_data\Drush\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Connection;
use Drupal\par_data\ParDataManagerInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * A Drush command file.
 */
final class ParDataCommands extends DrushCommands {

  /**
   * Constructs a ParDataCommands object.
   */
  public function __construct(
    private readonly ParDataManagerInterface $parDataManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly Connection $databaseConnection,
  ) {
    parent::__construct();
  }

  /**
   * Sanitise user data.
   */
  #[CLI\Command(name: 'par-data:sanitise-users', aliases: ['spp'])]
  #[CLI\Usage(name: 'par-data:sanitise-users', description: 'Sanitise user data')]
  public function sanitiseUserData() {
    // Drupal user.
    $this->output->writeln(dt('Sanitise Drupal Users the PAR way...'));
    $drupal_users = $this->databaseConnection->query("SELECT uid, name, mail FROM users_field_data WHERE uid > 1 AND mail NOT LIKE ''")->fetchAll();

    if (!empty($drupal_users)) {
      foreach ($drupal_users as $drupal_user) {
        $random_name = $this->randomNumber();
        $hash_email = md5($drupal_user->mail) . '@localhost.localdomain';

        $query = sprintf('UPDATE users_field_data SET init = \'%s\', mail = \'%s\', name = \'%s\' WHERE uid = %s',
          $hash_email,
          $hash_email,
          $random_name,
          $drupal_user->uid,
        );
        $this->databaseConnection->query($query)->execute();
      }
    }

    // PAR Data Person.
    $this->output->writeln(dt('Sanitise PAR Data Person records...'));
    $par_person_data = $this->databaseConnection->query("SELECT id, salutation, work_phone, mobile_phone, email, first_name, last_name FROM par_people_field_data")->fetchAll();

    if (!empty($par_person_data)) {
      foreach ($par_person_data as $person_data) {
        $hash_email = md5($person_data->email) . '@localhost.localdomain';

        $query = sprintf('UPDATE par_people_field_data SET salutation=\'%s\', work_phone=\'%s\', mobile_phone=\'%s\', email=\'%s\', first_name=\'%s\', last_name=\'%s\' WHERE id=%d',
          $this->randomNumber(),
          $this->randomNumber(),
          $this->randomNumber(),
          $hash_email,
          $this->randomNumber(),
          $this->randomNumber(),
          $person_data->id,
        );
        $this->databaseConnection->query($query)->execute();
      }
    }

    // PAR Data Person revisions.
    $this->output->writeln(dt('Sanitise PAR Data Person revision records...'));
    $par_person_revision_data = $this->databaseConnection->query("SELECT id, salutation, work_phone, mobile_phone, email, first_name, last_name FROM par_people_field_revision")->fetchAll();

    if (!empty($par_person_revision_data)) {
      foreach ($par_person_revision_data as $person_data_revision) {
        $hash_email = md5($person_data_revision->email) . '@localhost.localdomain';

        $query = sprintf('UPDATE par_people_field_revision SET salutation=\'%s\', work_phone=\'%s\', mobile_phone=\'%s\', email=\'%s\', first_name=\'%s\', last_name=\'%s\' WHERE id=%d',
          $this->randomNumber(),
          $this->randomNumber(),
          $this->randomNumber(),
          $hash_email,
          $this->randomNumber(),
          $this->randomNumber(),
          $person_data_revision->id,
        );
        $this->databaseConnection->query($query)->execute();
      }
    }
  }

  /**
   * Returns a 12 digit random number.
   *
   * @return int
   *   Random number.
   */
  public function randomNumber() {
    return rand(100000000000, 999999999999);
  }

  /**
   * Warm the PAR Data caches.
   */
  #[CLI\Command(name: 'par-data:cache-warm', aliases: ['pcw'])]
  #[CLI\Argument(name: 'type', description: 'The type of data to be warmed.')]
  #[CLI\Usage(name: 'par-data:cache-warm par_data_authority', description: 'Usage description')]
  public function cacheWarm($type) {
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
  #[CLI\Command(name: 'par-data:index-health', aliases: ['pih'])]
  #[CLI\Argument(name: 'index', description: 'The type of data to be warmed.')]
  #[CLI\Option(name: 'index-health', description: 'Whether to check the index health.')]
  #[CLI\Usage(name: 'par-data:index-health partnerships', description: 'Usage description')]
  public function indexHealth($index = NULL, array $options = ['index-health' => NULL]) {
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
  #[CLI\Command(name: 'par-data:update-registered-organisations', aliases: ['puro'])]
  #[CLI\Argument(name: 'register', description: 'The register to update or NULL to update entities not assigned to a register.')]
  #[CLI\Usage(name: 'par-data:update-registered-organisations companies_house', description: 'Usage description')]
  public function updateRegisteredOrganisations(?string $register = NULL) {
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
