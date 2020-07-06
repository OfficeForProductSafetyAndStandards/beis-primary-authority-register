<?php

namespace Drupal\par_log;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the audit_log tagged services to remove the default logging subscribers.
 */
class ParLogServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides language_manager class to test domain language negotiation.
    // Adds entity_type.manager service as an additional argument.
    $definition_ids = array_keys($container->findTaggedServiceIds('audit_log_event_subscriber'));

    foreach ($definition_ids as $definition_id) {
      $definition = $container->getDefinition($definition_id);

      if (!$definition->hasTag('par_log_event_subscriber')) {
        $definition->clearTag('audit_log_event_subscriber');
      }
    }
  }
}
