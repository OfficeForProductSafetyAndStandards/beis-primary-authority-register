<?php

namespace Drupal\par_cache\Drush\Commands;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Cache\Cache;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Drush commandfile.
 */
final class ParCacheCommands extends DrushCommands {

  /**
   * Constructs a ParCacheCommands object.
   */
  public function __construct(
    private readonly CacheFactoryInterface $cacheFactory,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_cache.persistent.cache_factory'),
    );
  }

  /**
   * Flush permanent cache bin.
   */
  #[CLI\Command(name: 'par_cache:clear', aliases: ['pcc'])]
  #[CLI\Argument(name: 'bin', description: 'Bin to flush cache of.')]
  #[CLI\Usage(name: 'par_cache:clear par_data', description: 'Usage description')]
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
    catch (\Exception) {
      $this->logger()->error(dt('@bin not a valid cache bin.', ['@bin' => $bin]));
    }
  }

}
