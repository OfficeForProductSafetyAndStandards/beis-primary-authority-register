<?php

namespace Drupal\par_cache\Cache;

use Drupal\redis\ClientFactory;

/**
 * Common code and client singleton, for all Redis clients.
 */
class RedisClientFactory extends ClientFactory {

  /**
   * Cache implementation namespace.
   */
  const REDIS_IMPL_CACHE = '\\Drupal\\par_cache\\Cache\\';
}

