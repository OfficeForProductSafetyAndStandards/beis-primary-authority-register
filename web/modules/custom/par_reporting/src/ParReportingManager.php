<?php

namespace Drupal\par_reporting;

use Drupal\par_reporting\Annotation\ParStatistic;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Manages all functionality universal to Par Data.
 */
class ParReportingManager extends DefaultPluginManager implements ParReportingManagerInterface, TrustedCallbackInterface {

  use LoggerChannelTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The length of time before statistic caches should be expired.
   */
  const CACHE_EXPIRY = "+1 day";

  /**
   * Loaded plugin Cache.
   */
  protected $stats = [];

  /**
   * Lists the trusted callbacks provided by this implementing class.
   *
   * Trusted callbacks are public methods on the implementing class and can be
   * invoked via
   * \Drupal\Core\Security\DoTrustedCallbackTrait::doTrustedCallback().
   *
   * @return string[]
   *   List of method names implemented by the class that can be used as trusted
   *   callbacks.
   *
   * @see \Drupal\Core\Security\DoTrustedCallbackTrait::doTrustedCallback()
   */
  #[\Override]
  public static function trustedCallbacks() {
    return ['render'];
  }

  /**
   * Get the cache bin.
   *
   * @return \Drupal\Core\Cache\CacheBackendInterface
   *   A cache bin instance.
   */
  public function getCacheBin(): CacheBackendInterface {
    return \Drupal::cache();
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   */
  public function getMessenger(): MessengerInterface {
    return \Drupal::service('messenger');
  }

  /**
   * Helper function to retrieve the current DateTime.
   *
   * Allows tests to modify the current time.
   */
  protected function getCurrentTime(): DrupalDateTime {
    return new DrupalDateTime('now');
  }

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
      ParStatisticInterface::class,
      ParStatistic::class
    );

    $this->alterInfo('par_statistic_info');
    $this->setCacheBackend($cache_backend, 'par_statistic_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
   * @return \Drupal\par_reporting\ParStatisticInterface
   */
  #[\Override]
  public function createInstance($plugin_id, array $configuration = []) {
    if (!isset($this->stats[$plugin_id])) {
      $this->stats[$plugin_id] = parent::createInstance($plugin_id, $configuration);
    }

    return $this->stats[$plugin_id];
  }

  /**
   * Get only the enabled rules.
   */
  #[\Override]
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
   * {@inheritDoc}
   */
  #[\Override]
  public function render(string $id): ?array {
    try {
      $definition = $this->getDefinition($id);
      $plugin = $definition ? $this->createInstance($definition['id'], $definition) : NULL;

      return $plugin instanceof ParStatisticInterface && $plugin->getStatus() ? $plugin->renderStat() : [];
    }
    catch (PluginException $e) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->error($e);
    }

    return [];
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function get(string $id): int {
    $cid = "par_reporting:stat:$id";
    $cache = $this->getCacheBin()->get($cid);
    // Return cached statistics if found.
    //    if ($cache) {
    //      return $cache->data;
    //    }.
    try {
      $definition = $this->getDefinition($id);
      $plugin = $definition ? $this->createInstance($definition['id'], $definition) : NULL;
    }
    catch (PluginException $e) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->error($e);
      $plugin = NULL;
    }

    // Get the statistic.
    $stat = $plugin instanceof ParStatisticInterface && $plugin->getStatus() ? (int) $plugin->getStat() : 0;

    // Cache the statistic.
    $expiry = $this->getCurrentTime();
    $expiry->modify(self::CACHE_EXPIRY);
    $this->getCacheBin()->set($cid, $stat, $expiry->getTimestamp());

    return $stat;
  }

}
