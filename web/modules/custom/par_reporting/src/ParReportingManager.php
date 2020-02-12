<?php

namespace Drupal\par_reporting;


use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
* Manages all functionality universal to Par Data.
*/
class ParReportingManager extends DefaultPluginManager implements ParReportingManagerInterface {

  use LoggerChannelTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

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
      'Plugin/ParStatistic',
      $namespaces,
      $module_handler,
      'Drupal\par_reporting\ParStatisticInterface',
      'Drupal\par_reporting\Annotation\ParStatistic'
    );

    $this->alterInfo('par_statistic_info');
    $this->setCacheBackend($cache_backend, 'par_statistic_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // Assign a default staleness for statistics.
    if (!isset($definition['staleness'])) {
      $definition['staleness'] = 3600;
    }
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\par_reporting\ParStatisticBaseInterface
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
   * @param string $id
   *   The ParStatistic plugin ID.
   *
   * @return array
   *   A rendered Statistic plugin.
   */
  public function render($id) {
    try {
      $definition = $this->getDefinition($id);
      $plugin = $definition ? $this->createInstance($definition['id'], $definition) : NULL;

      return $plugin->renderStat();
    }
    catch (PluginException $e) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->error($e);
    }
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   */
  public function getMessenger() {
    return \Drupal::service('messenger');
  }

}
