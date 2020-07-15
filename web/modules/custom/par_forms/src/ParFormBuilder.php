<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
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
   * Actions to perform on detecting an error.
   */
  const PAR_ERROR_IGNORE = '0';
  const PAR_ERROR_DISPLAY = '1';
  const PAR_ERROR_CLEAR = '2';

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
   * @param ParFormPluginInterface $component
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
    $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginNamespace()] = $component->getWrapper();

    // Count the current cardinality.
    $count = $component->getNewCardinality();

    for ($i = 1; $i <= $count; $i++) {
      // @TODO This is the only remaining bit when possible calls to `progress()`
      // aren't caught. They're used frequently in the form plugins, we need a way to safely
      // add redirection to plugins with the _same_ fallbacks as `ParBaseForm::submitForm()`
      // and `ParBaseController::getProceedingUrl()`.
      // Ideally coverting the hardcoded fallbacks in `ParBaseForm::submitForm()` into
      // FlowEventListeners so that they happen everywhere this is called.
      $element = $component->getElements([], $i);

      // Handle instances where FormBuilderInterface should return a redirect response.
      if ($element instanceof RedirectResponse) {
        return $element;
      }

      $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginNamespace()][$i-1] = $element;

      // Only show element actions for plugins with multiple cardinality
      if ($element_actions = $component->getElementActions($i)) {
        $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginNamespace()][$i-1] += $element_actions;
      }

      // Add the component element wrappers.
      $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginNamespace()][$i-1] += $component->getElementWrapper($i);
    }

    // Only show component actions for plugins with multiple cardinality
    if ($component_actions = $component->getComponentActions()) {
      $elements['actions'] = $component_actions;
    }

    return $elements;
  }

  public function validatePluginElements(ParFormPluginInterface $component, $form, FormStateInterface &$form_state, $cardinality = NULL) {
    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      // Handle instances where only a specific cardinality should be validated.
      if ($cardinality && $i !== $cardinality) {
        continue;
      }

      $action = ($component->getCardinality() === 1 || $i === 1 || $i < $count) ? self::PAR_ERROR_DISPLAY : self::PAR_ERROR_CLEAR;
      $component->validate($form, $form_state, $i, $action);
    }
  }

}
