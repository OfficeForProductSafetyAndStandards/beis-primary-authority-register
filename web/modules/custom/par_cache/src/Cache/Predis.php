<?php

namespace Drupal\par_cache\Cache;

use Drupal\redis\Cache\Predis;

/**
 * Defines a permanent Redis cache implementation.
 *
 * @ingroup cache
 */
class PhpRedisCacheBackend extends Predis {

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    // This cache doesn't need to be deleted when doing cache rebuild.
    // We do nothing here.
  }

  /**
   * Deletes all cache items in a bin when explicitly called.
   *
   * @see \Drupal\Core\Cache\DatabaseBackend::deleteAll()
   */
  public function deleteAllPermanent() {
    parent::deleteAll();
  }

  /**
   * {@inheritdoc}
   */
  public function removeBin() {
    parent::deleteAll();
  }

}
