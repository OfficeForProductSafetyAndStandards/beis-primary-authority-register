<?php

namespace Drupal\par_data_test\Commands;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\default_content\ImporterInterface;
use Drush\Commands\DrushCommands;

/**
 * Class DefaultContentCommands.
 *
 * @package Drupal\par_data_test
 */
class ParTestDataDrushCommands extends DrushCommands {

  /**
   * The default content exporter.
   *
   * @var ImporterInterface
   */
  protected $defaultContentImporter;

  /**
   * Constructor.
   *
   * @param ImporterInterface $default_content_importer
   *   The default content importer.
   */
  public function __construct(ImporterInterface $default_content_importer) {
    $this->defaultContentImporter = $default_content_importer;
  }

  public static function create(ContainerInterface $container): self {
    return new static($container->get('default_content.importer'));
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
  public function contentImportModule($module) {
    $this->defaultContentImporter->importContent($module);
  }

}
