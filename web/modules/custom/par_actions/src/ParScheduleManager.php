<?php

namespace Drupal\par_actions;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a PAR Schedule plugin manager.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ParScheduleManager extends DefaultPluginManager {

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
      'Plugin/ParSchedulerRule',
      $namespaces,
      $module_handler,
      'Drupal\par_actions\ParSchedulerRuleInterface',
      'Drupal\par_actions\Annotation\ParSchedulerRule'
    );

    $this->alterInfo('par_scheduler_info');
    $this->setCacheBackend($cache_backend, 'par_scheduler_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // Assign a default property to search for.
    if (!isset($definition['property'])) {
      $definition['property'] = 'created',
    }

    // Assign a default time to search since.
    if (isset($definition['time'])) {
      $definition['time'] += [
        'time' => '-5 days',
      ];
    }
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\par_actions\ParSchedulerRuleInterface
   */
  public function createInstance($plugin_id, array $configuration = []) {
    return parent::createInstance($plugin_id, $configuration);
  }

}
