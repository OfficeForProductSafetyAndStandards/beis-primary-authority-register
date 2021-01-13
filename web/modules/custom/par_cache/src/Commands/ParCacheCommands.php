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
   * ParCacheCommands constructor.
   *
   * @param \Drupal\Core\Cache\CacheFactoryInterface $cache_factory
   *   Cache Factory.
   */
  public function __construct(CacheFactoryInterface $cache_factory) {
    $this->cacheFactory = $cache_factory;
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
        $this->logger()->error(dt('@bin bin is not using pcb.', ['@bin' => $bin]));
      }
    }
    catch (\Exception $e) {
      $this->logger()->error(dt('@bin not a valid cache bin.', ['@bin' => $bin]));
    }
  }

  /**
   * Flush cache for all bins using permanent cache backend.
   *
   * @usage par-cache:clear-all
   *   Flush cache for all permanent cache backends.
   *
   * @command par-cache:clear-all
   * @aliases pcca
   */
  public function clearAll() {
    if (!$this->io()->confirm(dt('Are you sure you want to flush all permanent cache bins?'))) {
      throw new UserAbortException();
    }

    foreach (Cache::getBins() as $bin => $backend) {
      if (method_exists($backend, 'deleteAllPermanent')) {
        $backend->deleteAllPermanent();
        $this->logger()->success(dt('Flushed all cache for @bin.', ['@bin' => $bin]));
      }
    }
  }

  /**
   * List permanent cache bins.
   *
   * @usage par-cache:list
   *   List all bins using permanent backends.
   *
   * @command par-cache:list
   * @aliases pcl
   */
  public function listBins() {
    $bins = Cache::getBins();

    foreach ($bins as $bin => $object) {
      if (method_exists($object, 'deleteAllPermanent')) {
        $this->io()->writeln($bin);
      }
    }

    $this->io()->writeln('');
  }

}
