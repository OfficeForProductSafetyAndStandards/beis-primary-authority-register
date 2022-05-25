<?php

namespace Drupal\companies;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Http\Adapter\Guzzle6\Client;

/**
 * Service class for GovUK Notify.
 */
class CompaniesManager extends DefaultPluginManager {

  /**
   * Constructs a ParScheduleManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/CompaniesRegister',
      $namespaces,
      $module_handler,
      'Drupal\companies\CompaniesRegisterInterface',
      'Drupal\companies\Annotation\CompaniesRegister'
    );

    $this->alterInfo('companies_info');
    $this->setCacheBackend($cache_backend, 'companies_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

  public function getClient() {
    return client;
  }
}
