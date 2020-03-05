<?php

namespace Drupal\par_data_test_entity\Plugin;

use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Component\Plugin\Discovery\StaticDiscovery;
use Drupal\Component\Plugin\Factory\DefaultFactory;

/**
 * Defines a plugin manager used by Plugin API unit tests.
 */
class TestSchedulerManager extends PluginManagerBase {

  public function __construct() {

    // Create the object that can be used to return definitions for all the
    // plugins available for this type. Most real plugin managers use a richer
    // discovery implementation, but StaticDiscovery lets us add some simple
    // mock plugins for unit testing.
    $this->discovery = new StaticDiscovery();

    // A plugin to test all events that should happen before the entity date.
    // Note: For the comparison to work the time property should be positive.
    $this->discovery->setDefinition('test_distant_before', [
      'label' => 'Test Scheduler Distant Past',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '+3 months',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);
    // A plugin to test past events.
    $this->discovery->setDefinition('test_before_working', [
      'label' => 'Test Scheduler Past',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '+6 working days',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);
    // A plugin to test past events.
    $this->discovery->setDefinition('test_before', [
      'label' => 'Test Scheduler Past',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '+6 days',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);

    // A plugin to test all past events including those of the present day.
    $this->discovery->setDefinition('test_current', [
      'label' => 'Test Scheduler Present',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '0 days',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);

    // A plugin to test all events that should happen after the entity date.
    // Note: For the comparison to work the time property should be negative.
    $this->discovery->setDefinition('test_after_working', [
      'label' => 'Test Scheduler Future',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '-6 working days',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);
    // A plugin to test all events that should happen after the entity date.
    $this->discovery->setDefinition('test_after', [
      'label' => 'Test Scheduler Future',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '-6 days',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);
    // A plugin to test all distant future events.
    $this->discovery->setDefinition('test_distant_after', [
      'label' => 'Test Scheduler Future',
      'entity' => 'par_data_test_entity',
      'property' => 'expiry_date',
      'time' => '-3 weeks',
      'class' => 'Drupal\par_data_test_entity\Plugin\test_plugins\ParExpiryTest',
    ]);

    // In addition to finding all of the plugins available for a type, a plugin
    // type must also be able to create instances of that plugin. For example, a
    // specific instance of a "User login" block, configured with a custom
    // title. To handle plugin instantiation, plugin managers can use one of the
    // factory classes included with the plugin system, or create their own.
    // DefaultFactory is a simple, general purpose factory suitable for
    // many kinds of plugin types. Factories need access to the plugin
    // definitions (e.g., since that's where the plugin's class is specified),
    // so we provide it the discovery object.
    $this->factory = new DefaultFactory($this->discovery);
  }

}
