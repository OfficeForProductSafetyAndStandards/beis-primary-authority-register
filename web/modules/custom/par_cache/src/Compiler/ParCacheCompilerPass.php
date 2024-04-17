<?php

namespace Drupal\par_cache\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 */
class ParCacheCompilerPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container) {
    $cache_lifecycle_bins = [];
    foreach ($container->findTaggedServiceIds('cache.bin') as $id => $attributes) {
      $bin = substr($id, strpos($id, '.') + 1);
      // Any bin with a lifecycle attribute on the service tag is included.
      if (isset($attributes[0]['lifecycle'])) {
        $cache_lifecycle_bins[$bin] = $attributes[0]['lifecycle'];
      }
    }

    $container->setParameter('cache_lifecycle_bins', $cache_lifecycle_bins);
  }

}
