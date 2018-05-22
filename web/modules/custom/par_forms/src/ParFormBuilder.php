<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a PAR Form Builder plugin manager.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ParFormBuilder extends DefaultPluginManager {

  use LoggerChannelTrait;
  use StringTranslationTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The logger channel to use.
   */
  const PAR_COMPONENT_PREFIX = 'par_component_';

  /**
   * Constructs a ParScheduleManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ParForm',
      $namespaces,
      $module_handler,
      'Drupal\par_forms\ParFormPluginInterface',
      'Drupal\par_forms\Annotation\ParForm'
    );

    $this->alterInfo('par_form_info');
    $this->setCacheBackend($cache_backend, 'par_form_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\par_forms\ParFormPluginBase
   */
  public function createInstance($plugin_id, array $configuration = []) {
    $plugin = parent::createInstance($plugin_id, $configuration);

    $plugin->setConfiguration($configuration);

    return $plugin;
  }

  /**
   * Helper to load the data for this plugin.
   *
   * @param $component
   */
  public function loadPluginData($component) {
    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      $component->loadData($i);
    }
  }

  /**
   * Helper to get all the elements from a plugin.
   *
   * @param ParFormPluginBaseInterface $component
   *   The plugin to load elements for.
   * @param array $elements
   *   An array to add the elements to.
   * @param mixed $cardinality
   *   If chosen only the specified cardinality will be displayed.
   *
   * @return array
   */
  public function getPluginElements($component, &$elements = [], $cardinality = NULL) {
    // Add all the registered components to the form.
    $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginId()] = $component->getWrapper();

    // Count the current cardinality.
    $count = $component->getNewCardinality();
    for ($i = 1; $i <= $count; $i++) {
      $element = $component->getElements([], $i);

      // Handle instances where FormBuilderInterface should return a redirect response.
      if ($element instanceof RedirectResponse) {
        return $element;
      }

      $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginId()][$i-1] = $element;

      // Only show element actions for plugins with multiple cardinality
      if ($element_actions = $component->getElementActions($i)) {
        $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginId()][$i-1] += $element_actions;
      }
    }

    // Only show component actions for plugins with multiple cardinality
    if ($component_actions = $component->getComponentActions()) {
      $elements['actions'] = $component_actions;
    }

    return $elements;
  }

  public function validatePluginElements($component, $form_state, $cardinality = NULL) {
    $violations = [];

    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      // Handle instances where only a specific cardinality should be validated.
      if ($cardinality && $i !== $cardinality) {
        continue;
      }

      $violations[$component->getPluginId()][$i] = $component->validate($form_state, $i);
    }

    return $violations;
  }

}
