<?php

namespace Drupal\par_cache\Cache;

use Drupal\Core\Cache\DatabaseBackendFactory as DefaultFactory;

class DatabaseBackendFactory extends DefaultFactory {

  /**
   * {@inheritDoc}
   */
  public function get($bin) {
    $max_rows = $this->getMaxRowsForBin($bin);
    return new CacheDatabaseBackend($this->connection, $this->checksumProvider, $bin, $max_rows);
  }

}
