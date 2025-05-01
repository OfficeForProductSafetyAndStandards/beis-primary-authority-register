<?php

namespace Drupal\par_data\Drush\Commands;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\search_api\ConsoleException;
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
    $this->output->writeln(t('Sanitise Drupal Users the PAR way...')->render());
    $drupal_users = $this->databaseConnection->query(
      "SELECT uid, mail FROM {users_field_data} WHERE uid > 1 AND mail NOT LIKE ''"
    )->fetchAll();

    if (!empty($drupal_users)) {
      foreach ($drupal_users as $drupal_user) {
        $hash_email = md5($drupal_user->mail) . '@localhost.localdomain';

        $this->databaseConnection->query(
          'UPDATE {users_field_data} SET init = :init, mail = :mail, name = :name WHERE uid = :uid',
          [
            'init' => $hash_email,
            'mail' => $hash_email,
            'name' => $this->randomNumber(),
            'uid' => $drupal_user->uid,
          ]
        );
      }
    }

    // PAR Data Person.
    $this->output->writeln(t('Sanitise PAR Data Person records...')->render());
    $par_person_data = $this->databaseConnection->query(
      'SELECT id, email FROM {par_people_field_data}'
    )->fetchAll();

    if (!empty($par_person_data)) {
      foreach ($par_person_data as $person_data) {
        $hash_email = md5($person_data->email) . '@localhost.localdomain';

        $this->databaseConnection->query(
          'UPDATE {par_people_field_data} SET salutation=:salutation, work_phone=:work_phone, mobile_phone=:mobile_phone, email=:email, first_name=:first_name, last_name=:last_name WHERE id=:id',
          [
            'salutation' => $this->randomNumber(),
            'work_phone' => $this->randomNumber(),
            'mobile_phone' => $this->randomNumber(),
            'email' => $hash_email,
            'first_name' => $this->randomNumber(),
            'last_name' => $this->randomNumber(),
            'id' => $person_data->id,
          ]
        );
      }
    }

    // PAR Data Person revisions.
    $this->output->writeln(t('Sanitise PAR Data Person revision records...')->render());
    $par_person_revision_data = $this->databaseConnection->query(
      'SELECT revision_id, email FROM {par_people_field_revision}'
    )->fetchAll();

    if (!empty($par_person_revision_data)) {
      foreach ($par_person_revision_data as $person_data_revision) {
        $hash_email = md5($person_data_revision->email) . '@localhost.localdomain';

        $this->databaseConnection->query(
          'UPDATE {par_people_field_revision} SET salutation=:salutation, work_phone=:work_phone, mobile_phone=:mobile_phone, email=:email, first_name=:first_name, last_name=:last_name WHERE revision_id=:revision_id',
          [
            'salutation' => $this->randomNumber(),
            'work_phone' => $this->randomNumber(),
            'mobile_phone' => $this->randomNumber(),
            'email' => $hash_email,
            'first_name' => $this->randomNumber(),
            'last_name' => $this->randomNumber(),
            'revision_id' => $person_data_revision->revision_id,
          ]
        );
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
    return random_int(100000000000, 999999999999);
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
        // By default it's the relationship cache's responsible for
        // entity management we want to warm.
        $entity->getRelationships();

        $unique_function_id = "getRelationships:{$entity->uuid()}:null:null";
        drupal_static_reset($unique_function_id);

        // Assess memory usage.
        if ($count % 100 == 0 && $count > 0) {
          $memory = round(memory_get_usage() / 1024 / 1024, 2);
          $this->output->writeln(
            t(
              '@memory MB used in the generation of %count caches',
              [
                '@memory' => $memory,
                '%count' => $count,
              ]
            )->render()
          );
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
        throw new ConsoleException(t('Server for index %index is not valid.', ['%index' => $index->id()])->render());
      }
      if ($include_index_health and !$index_health) {
        throw new ConsoleException(t('Index %index has only indexed %indexed out of %total items (%count entities).', [
          '%index' => $index->id(),
          '%indexed' => $indexed,
          '%total' => $total,
          '%count' => $count,
        ])->render());
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
    catch (PluginNotFoundException) {
      return "Failed to convert legacy legal entities.";
    }

    return "All legacy entities updated for processing.";
  }

}
