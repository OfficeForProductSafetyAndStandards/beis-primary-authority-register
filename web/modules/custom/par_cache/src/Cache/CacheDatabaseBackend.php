<?php

namespace Drupal\par_cache\Cache;

use Drupal\Core\Cache\DatabaseBackend;

class CacheDatabaseBackend extends DatabaseBackend {

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
