<?php

namespace Drupal\par_data_test\Drush\Commands;

use Drupal\default_content\ImporterInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Drush commandfile.
 */
final class ParDataTestCommands extends DrushCommands {

  /**
   * Constructs a ParDataTestCommands object.
   */
  public function __construct(
    private readonly ImporterInterface $defaultContentImporter,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('default_content.importer'),
    );
  }

  /**
   * Re-imports all the content defined in a module info file.
   *
   * @param string $module
   *   The name of the module.
   *
   * @command default-content:import-module
   * @aliases dcim
   */
  #[CLI\Command(name: 'par_data_test:import-module', aliases: ['dcim'])]
  #[CLI\Argument(name: 'module', description: 'The name of the module.')]
  #[CLI\Usage(name: 'par_data_test:import-module par_data_test', description: 'Usage description')]
  public function contentImportModule($module) {
    $this->defaultContentImporter->importContent($module);
  }

}
