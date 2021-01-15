<?php

namespace Drupal\par_cache\Cache;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Wraps a cache factory to register all calls to the cache system.
 */
class CachePersistentFactory implements CacheFactoryInterface, ContainerAwareInterface {
  use ContainerAwareTrait;

  const CACHE_LIFECYCLE_PERSIST = 'persistent';

  /**
   * The decorated cache factory.
   *
   * @var \Drupal\Core\Cache\CacheFactoryInterface
   */
  protected $cacheFactory;

  /**
   * The site settings.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * A list of cache bins that use backend wrapper with alternative lifecycles.
   *
   * @var array
   */
  protected $cacheLifecycleBins;

  /**
   * All wrapped cache backends.
   *
   * @var \Drupal\webprofiler\Cache\CacheBackendWrapper[]
   */
  protected $cacheBackends = [];

  /**
   * Constructs CacheFactory object.
   *
   * @param \Drupal\Core\Cache\CacheFactoryInterface $cache_factory
   *   The cache factory.
   * @param \Drupal\Core\Site\Settings $settings
   *   The site settings.
   * @param array $cache_lifecycle_bins
   *   (optional) A mapping of bins that have lifecycle settings.
   */
  public function __construct(CacheFactoryInterface $cache_factory, Settings $settings, array $cache_lifecycle_bins = []) {
    $this->cacheFactory = $cache_factory;
    $this->settings = $settings;
    $this->cacheLifecycleBins = $cache_lifecycle_bins;
  }

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    if (!isset($this->cacheBackends[$bin])) {
      $cache_lifecycle_settings = $this->settings->get('cache_lifecycle_bins');
      // First, look for a cache bin specific setting.
      if (isset($cache_lifecycle_settings['bins'][$bin])) {
        $lifecycle = $cache_lifecycle_settings['bins'][$bin];
      }
      // Second, use the default backend specified by the cache bin.
      elseif (isset($this->cacheLifecycleBins[$bin])) {
        $lifecycle = $this->cacheLifecycleBins[$bin];
      }

      $cache_backend = $this->cacheFactory->get($bin);
      if ($lifecycle === self::CACHE_LIFECYCLE_PERSIST) {
        $this->cacheBackends[$bin] = new CachePersistentBackendWrapper($cache_backend, $bin);
      }
      else {
        $this->cacheBackends[$bin] = $cache_backend;
      }
    }

    return $this->cacheBackends[$bin];
  }

}
