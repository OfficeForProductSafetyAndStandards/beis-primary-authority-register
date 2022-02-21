# PAR Caching

This modules creates some persistent cache bins that won't be cleared when flushing drupal caches. This enables deployments to be scheduled without clearing user data defined caches.

## Cache bins
* **par_actions** - for storing execution times of scheduler action executions so that they're not run repeatedly.
* **par_data** - for storing relationships between par_data entities, such that relationship trees can be easily referenced.
* **par_flows** - for storing multistep form caches and user temporary form data between sessions.

**Note:** Other modules may rely on the existence of these bing, but theses modules cannot declare dependencies on this module due to the nature of cache backend instantiation within Drupal. If this modules is disabled it may disrupt cache behaviour of the above functions.

## Clearing persistent caches
The following drush commands are available to clear the persistent cache backends:
* drush par-cache:clear _{bin}_

It is up-to developers to clear these bins if there are changes to the structure of the stored cache objects that would require a rebuild.

## Instalation
Any cache bin use these persistent caches by setting the backend to one of:
* `cache.backend.par_cache.database`
* `cache.backend.par_cache.redis`

Existing cache bins can be set through settings overrides:
```php
$settings['cache']['bins']['par_data'] = 'cache.backend.par_cache.redis';
```

New caches can be created with these backends in any modules services.yml file:
```yaml
cache.cache_name:
  class: Drupal\Core\Cache\CacheBackendInterface
  tags:
    - { name: cache.bin, default_backend: cache.backend.par_cache.database }
  factory: cache_factory:get
  arguments: [ cache_name ]
```

### Enabling the backend
To enable the backends at the same time as enabling the module the drupal boostrap container must be aware of the cache backends. To do so register the par_cache.services.yml in the Drupal settings file:
```php
$settings['container_yamls'][] = 'modules/custom/par_cache/par_cache.services.yml';
$class_loader->addPsr4('Drupal\\par_cache\\', 'modules/custom/par_cache/src');
```
