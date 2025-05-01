<?php

namespace Drupal\par_cache\Cache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;

/**
 * Wraps an existing cache backend to track calls to the cache backend.
 */
class CachePersistentBackendWrapper implements CacheBackendInterface, CacheTagsInvalidatorInterface {

  /**
   * The wrapped cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Constructs a new CacheBackendWrapper.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The wrapped cache backend.
   * @param string $bin
   *   The name of the wrapped cache bin.
   */
  public function __construct(CacheBackendInterface $cacheBackend, /**
   * The name of the wrapped cache bin.
   */
  protected $bin) {
    $this->cacheBackend = $cacheBackend;
  }

  /**
   * Call any missing methods on the decorated service.
   */
  public function __call($method, $args) {
    return call_user_func_array([$this->cacheBackend, $method], $args);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function deleteAll() {
    // This cache doesn't need to be deleted when doing cache rebuild.
    // We do nothing here.
  }

  /**
   * Deletes all cache items in a bin when explicitly called.
   */
  public function deleteAllPermanent() {
    $this->cacheBackend->deleteAll();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function removeBin() {
    $this->cacheBackend->deleteAll();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function get($cid, $allow_invalid = FALSE) {
    return $this->cacheBackend->get($cid, $allow_invalid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getMultiple(&$cids, $allow_invalid = FALSE) {
    return $this->cacheBackend->getMultiple($cids, $allow_invalid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function set($cid, $data, $expire = Cache::PERMANENT, array $tags = []) {
    return $this->cacheBackend->set($cid, $data, $expire, $tags);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setMultiple(array $items) {
    return $this->cacheBackend->setMultiple($items);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function delete($cid) {
    return $this->cacheBackend->delete($cid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function deleteMultiple(array $cids) {
    return $this->cacheBackend->deleteMultiple($cids);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function invalidate($cid) {
    return $this->cacheBackend->invalidate($cid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function invalidateMultiple(array $cids) {
    return $this->cacheBackend->invalidateMultiple($cids);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function invalidateTags(array $tags) {
    if ($this->cacheBackend instanceof CacheTagsInvalidatorInterface) {
      $this->cacheBackend->invalidateTags($tags);
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function invalidateAll() {
    return $this->cacheBackend->invalidateAll();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function garbageCollection() {
    return $this->cacheBackend->garbageCollection();
  }

}
