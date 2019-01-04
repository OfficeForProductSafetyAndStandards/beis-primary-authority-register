<?php

namespace Drupal\par_data_test;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\Exception\UnknownExtensionException;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\InfoParser;
use Drupal\Core\Extension\InfoParserException;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\State\State;
use Drupal\Core\State\StateInterface;
use Drupal\default_content\Importer;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
* A controller for resetting par data test content.
*/
class ParDataTestManager extends ControllerBase {

  /**
   * The default content importer.
   *
   * @var \Drupal\default_content\Importer
   */
  protected $importer;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * The module list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleList;

  /**
   * The info parser.
   *
   * @var \Drupal\Core\Extension\InfoParser
   */
  protected $infoParser;

  /**
   * The state store.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a ParDataTestManager instance.
   *
   * @param \Drupal\default_content\Importer
   *   The default content importer.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The entity type manager.
   * @param \Drupal\Core\Extension\ModuleHandler
   *   The module handler.
   * @param \Drupal\Core\Extension\ModuleExtensionList
   *   The module list.
   * @param \Drupal\Core\Extension\InfoParser
   *   The info parser.
   * @param \Drupal\Core\State\StateInterface
   *   The state store.
   * @param \Drupal\Core\Messenger\MessengerInterface
   *   The messenger.
   */
  public function __construct(Importer $importer, EntityTypeManagerInterface $entity_type_manager, ModuleHandler $module_handler, ModuleExtensionList $module_list, InfoParser $info_parser, StateInterface $state, MessengerInterface $messenger) {
    $this->importer = $importer;
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->moduleList = $module_list;
    $this->infoParser = $info_parser;
    $this->state = $state;
    $this->messenger = $messenger;
  }

  /**
   * Dynamic getter for the content importer.
   *
   * @return \Drupal\default_content\Importer
   */
  public function getContentImporter() {
    return $this->importer;
  }

  /**
   * Dynamic getter for the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Dynamic getter for the module handler.
   *
   * @return \Drupal\Core\Extension\ModuleHandler
   */
  public function getModuleHandler() {
    return $this->moduleHandler;
  }

  /**
   * Dynamic getter for the module list.
   *
   * @return \Drupal\Core\Extension\ModuleExtensionList
   */
  public function getModuleList() {
    return $this->moduleList;
  }

  /**
   * Dynamic getter for the info parser.
   *
   * @return \Drupal\Core\Extension\InfoParser
   */
  public function getInfoParser() {
    return $this->infoParser;
  }

  /**
   * Dynamic getter for the state store.
   *
   * @return \Drupal\Core\State\StateInterface
   */
  public function state() {
    return $this->state;
  }

  /**
   * Dynamic getter for the messenger.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   */
  public function messenger() {
    return $this->messenger;
  }

  /**
   * Import all data including that of any secondary par data modules.
   *
   * Secondary data is defined as any default_content modules within the same 'PAR Test Data' package of modules.
   * @see self:;getDataModules()
   */
  public function importData() {
    foreach ($this->getDataModules() as $module) {
      $this->getContentImporter()->importContent($module->getName(), TRUE);
    }
  }

  /**
   * Remove all secondary test data.
   *
   * Secondary data is defined as any default_content modules within the same 'PAR Test Data' package of modules.
   * @see self:;getDataModules()
   */
  public function removeData() {
    foreach ($this->getDataModules() as $module) {
      $entities = $this->getDataEntities($module);

      // Permanently delete all entities.
      foreach ($entities as $entity) {
        if ($entity instanceof ParDataEntityInterface) {
          // Remove the entity permanently.
          $entity->destroy();
        }
        else {
          $entity->delete();
        }
      }
    }
  }

  /**
   * Get the package name for par data modules.
   *
   * @return string|null
   *   The name of the package for par data modules.
   */
  public function getPackageName() {
    try {
      $test_data_module = $this->getModuleHandler()->getModule('par_data_test');
      $test_data_module->info = $this->getInfoParser()->parse($test_data_module->getPathname());
      $package = $test_data_module->info['package'];
      $this->state()->set('par_data_test.package', $package);
    }
      // If the module is not active we can try to get the last known
    catch (UnknownExtensionException $e) {
      $package = $this->state()->get('par_data_test.package');
    }
    catch (InfoParserException $e) {
      $this->messenger()->addError($this->t('Module could not be loaded due to an error: %error', ['%error' => $e->getMessage()]));
    }

    return isset($package) ? $package : NULL;
  }

  /**
   * Return other active par data modules.
   *
   * @return array
   */
  public function getDataModules() {
    if ($package = $this->getPackageName()) {
      try {
        $modules = $this->getModuleList()->reset()->getList();

        // Filter modules to match only those additional, enabled par data modules.
        $modules = array_filter($modules, function ($module) use ($package) {
          return ($package === $module->info['package']
            && $module->getPath() . '/content')
            && $this->getModuleHandler()->moduleExists($module->getName());
        });
      }
      catch (InfoParserException $e) {
        $this->messenger()->addError($this->t('Modules could not be acquired due to an error: %error', ['%error' => $e->getMessage()]));
      }
    }

    return isset($modules) ? $modules : [];
  }

  /**
   * Return all entities related to a given module.
   */
  public function getDataEntities(Extension $module) {
    $module_folder = $module->getPath() . '/content';

    // Scan through all entity folders and remove the entities along with their references.
    $entity_folders = array_filter(scandir($module_folder), function ($item) {
      return $item[0] !== '.';
    });

    $entities = [];
    foreach ($entity_folders as $directory) {
      $entity_uuids = array_filter(scandir($module_folder . '/' . $directory), function ($item) {
        return $item[0] !== '.';
      });

      $storage = $this->getEntityTypeManager()->getStorage($directory);
      foreach ($entity_uuids as $filename) {
        $uuid = rtrim($filename, '.json');

        foreach ($storage->loadByProperties(['uuid' => $uuid]) as $entity) {
          $entities[$entity->uuid()] = $entity;

          // Get all related entities also.
          if ($entity instanceof ParDataEntityInterface) {
            foreach ($entity->getDependents() as $dependent) {
              $entities[$dependent->uuid()] = $dependent;
            }
          }
        }
      }
    }

    return $entities;
  }

}
