<?php

namespace Drupal\par_cache\Commands;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Cache\Cache;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;


/**
 * Drush commands for PAR Caches.
 */
class ParCacheCommands extends DrushCommands {

  /**
   * Cache Factory.
   *
   * @var \Drupal\Core\Cache\CacheFactoryInterface
   */
  private $cacheFactory;

  /**
   * A list of cache bins that use backend wrapper with alternative lifecycles.
   *
   * @var array
   */
  protected $cacheLifecycleBins = [];

  /**
   * ParCacheCommands constructor.
   *
   * @param \Drupal\Core\Cache\CacheFactoryInterface $cache_factory
   *   Cache Factory.
   */
  public function __construct(CacheFactoryInterface $cache_factory) {
    $this->cacheFactory = $cache_factory;
  }

  public static function create(ContainerInterface $container, DrushContainer $drush): self {
      return new static(
        $container->get('par_cache.persistent.cache_factory')
      );
  }

  /**
   * Flush permanent cache bin.
   *
   * @param string $bin
   *   Bin to flush cache of.
   *
   * @usage par-cache:clear bin
   *   Flush cache for particular bin.
   *
   * @command par-cache:clear
   * @aliases pcc
   */
  public function clear(string $bin) {
    try {
      $cache = $this->cacheFactory->get($bin);

      if (method_exists($cache, 'deleteAllPermanent')) {
        $cache->deleteAllPermanent();
        $this->logger()->success(dt('Deleted all cache for @bin.', ['@bin' => $bin]));
      }
      else {
        $this->logger()->error(dt('@bin bin is not using par cache backend.', ['@bin' => $bin]));
      }
    }
    catch (\Exception $e) {
      $this->logger()->error(dt('@bin not a valid cache bin.', ['@bin' => $bin]));
    }
  }

}
