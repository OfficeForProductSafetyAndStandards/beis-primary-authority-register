<?php

namespace Drupal\par_cache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\par_cache\Compiler\ParCacheCompilerPass;

class ParCacheServiceProvider extends ServiceProviderBase {

  /**
  * {@inheritdoc}
  */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new ParCacheCompilerPass());
  }

}
