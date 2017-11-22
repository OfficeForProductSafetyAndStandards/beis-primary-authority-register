<?php

namespace Drupal\par_actions;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Logger\LoggerChannelTrait;

/**
 * Provides a PAR Schedule plugin manager.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ParScheduleManager extends DefaultPluginManager {

  use LoggerChannelTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The minimum interval required between runs.
   *
   * This allows the queued items to be processed before the next run.
   */
  const MIN_INTERVAL = 3600;

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
   * Returns the logger channel specific to errors logged by PAR Actions.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // Assign a default property to search for.
    if (!isset($definition['property'])) {
      $definition['property'] = 'created';
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

  /**
   * Get only the enabled rules.
   */
  public function getDefinitions($only_active = FALSE) {
    $definitions = [];
    foreach (parent::getDefinitions() as $id => $definition) {
      if (!$only_active || !empty($definition['status'])) {
        $definitions[$id] = $definition;
      }
    }

    return $definitions;
  }

  /**
   * A helper method to run any plugin instance.
   *
   * @param $definition
   *   The ParScheduleRule plugin definition to run.
   */
  public function run($definition) {
    $plugin = $this->createInstance($definition['id'], $definition);

    try {
      // Ensure that scheduler plugins cannot be run more frequently
      // than the minimum interval time.
      $last_run = $this->getSchedulerLastRun($plugin);
      if ($last_run >= REQUEST_TIME - self::MIN_INTERVAL) {
        return;
      }

      $this->setSchedulerLastRun($plugin);

      $plugin->run();
    }
    catch (ParActionsException $e) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->error($e);
    }
  }

  /**
   * Helper method to check when a plugin last ran.
   *
   * @param $plugin
   *   The plugin to check when it last ran.
   *
   * @return mixed
   *   The timestamp when this plugin was last run.
   */
  public function getSchedulerLastRun($plugin) {
    return \Drupal::state()->get("par_actions.{$plugin->getPluginId()}.last_schedule", 0);
  }

  /**
   * Helper method to set when the plugin runs.
   *
   * @param $plugin
   *   The plugin to check when it last ran.
   */
  public function setSchedulerLastRun($plugin) {
    \Drupal::state()->set("par_actions.{$plugin->getPluginId()}.last_schedule", REQUEST_TIME);
  }

}
