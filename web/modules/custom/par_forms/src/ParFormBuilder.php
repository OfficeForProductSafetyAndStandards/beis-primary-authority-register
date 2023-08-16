<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Utility\Html;
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
class ParFormBuilder extends DefaultPluginManager implements ParFormBuilderInterface {

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
   * @return ParFormPluginInterface
   */
  public function createInstance($plugin_id, array $configuration = []): ParFormPluginInterface {
    /** @var ParFormPluginInterface $plugin */
    $plugin = parent::createInstance($plugin_id, $configuration);

    $plugin->setConfiguration($configuration);

    return $plugin;
  }

  /**
   * Helper to load the data for this plugin.
   *
   * @param ParFormPluginInterface $component
   *   The form plugin to load data for.
   */
  public function loadData(ParFormPluginInterface $component): void {
    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      $component->loadData($i);
    }
  }

  /**
   * Build the component.
   *
   * @param ParFormPluginInterface $component
   *   The form component plugin to build.
   * @param ?int $index
   *   A index value to influence which elements are displayed.
   *
   * @return array|RedirectResponse
   *   The form elements for the rendered component.
   */
  public function build(ParFormPluginInterface $component, int $index = NULL): array|RedirectResponse {
    // If the component supports a summary list
    if ($this->supportsSummaryList($component)) {
      // If a specific index is requested build it.
      return $this->displaySummaryList($component, $index) ?
        $this->getSummaryList($component) :
        $this->getElements($component, $index);
    }
    else {
      return $this->getElements($component);
    }
  }

  public function validate(ParFormPluginInterface $component, $form, FormStateInterface &$form_state, $index = NULL): void {
    $count = $component->countItems();

    // The delta of the element is zero-based whereas the index starts at 1.
    $delta = $index - 1;

    // Get the maximum index (for a new item).
    $max = $component->getNewCardinality();
    for ($i = 1; $i <= $max; $i++) {
      // For components that use the Summary List pattern, single indexes should be validated only.
      if ($this->supportsSummaryList($component) && $index && $i !== $index) {
        // If it is not the requested index don't validate the elements.
        continue;
      }

      // There are two actions to perform when validating component data, errors
      // can either be set or the data can be cleared from the form.
//      $action = ($component->getCardinality() === 1 || $i === 1 || $i < $count) ? self::PAR_ERROR_DISPLAY : self::PAR_ERROR_CLEAR;
      $action = self::PAR_ERROR_DISPLAY;

      // Only validate if there is data.
      if ($component->getDataItem($i)) {
        $item_cardinality = $i + 1;
        $component->validate($form, $form_state, $item_cardinality, $action);
      }
    }
  }

  /**
   * Whether the plugin supports a summary list.
   *
   * Form components support the summary list display if:
   *  - They have the ParSummaryListInterface
   *  - The plugin instance is configured for multiple cardinality
   */
  public function supportsSummaryList(ParFormPluginInterface $component): bool {
    return $component instanceof ParSummaryListInterface &&
      $component->isMultiple();
  }

  /**
   * Whether the plugin should display a summary list.
   *
   * Only display a summary list if:
   *  - The plugin supports the summary list display @see self::supportsSummaryList()
   *  - The plugin has data already set
   *  - The plugin is not set to display a specific index
   */
  public function displaySummaryList(ParFormPluginInterface $component, int $index = NULL): bool {
    return $this->supportsSummaryList($component) &&
      !empty($component->getData()) &&
      !$index;
  }

  /**
   * Get plugin summary list.
   *
   * The summary list helps show the data that has already been added and
   * enables complex form plugins with multiple cardinality to add, change
   * and remove values before submission.
   *
   * A plugin will only support the summary list component if it supports:
   *  - multiple cardinality
   *  - SummaryListInterface
   *
   * @param ParFormPluginInterface $component
   *   The plugin to load the summary list for.
   *
   * @return ?array
   *   The form array to build the summary list for this component.
   *
   * @see https://github.com/alphagov/govuk-design-system-backlog/issues/21
   * @see https://design-system.service.gov.uk/components/summary-list
   */
  protected function getSummaryList(ParFormPluginInterface $component): ?array {
    // Summary lists can only be displayed under specific conditions.
    if (!$this->supportsSummaryList($component)) {
        return NULL;
    }

    // Generate the summary list for this component.
    if ($summary_list = $component->getSummaryList()) {
      // Create the base element.
      $elements = [
        $component->getPrefix() => $component->getWrapper()
      ];
      $elements[$component->getPrefix()]['#attributes']['class'][] = 'component-summary-list';

      // Add the summary list.
      $elements[$component->getPrefix()] += $summary_list;

      // Always show the 'add another' button on the summary list.
      if ($component_actions = $component->getComponentActions()) {
        $elements[$component->getPrefix()] += [
          'actions' => $component_actions,
        ];
      }
    }

    return $elements;
  }

  /**
   * Helper to get all the elements from a plugin.
   *
   * @param ParFormPluginInterface $component
   *   The plugin to load elements for.
   * @param ?int $index
   *   If chosen only the specified index will be displayed.
   *
   * @return mixed
   *   Typically, an array of plugin form elements.
   *   If the cardinality is given it can be a single element.
   *   Alternatively a RedirectResponse.
   */
  protected function getElements(ParFormPluginInterface $component, int $index = NULL): mixed {
    // Create the base element.
    $elements = [
      $component->getPrefix() => $component->getWrapper()
    ];

    // Get the maximum index (for a new item).
    $max = $component->getNewCardinality();
    for ($i = 1; $i <= $max; $i++) {
      // For components that use the Summary List pattern, single indexes can be shown.
      if ($this->supportsSummaryList($component) && $index && $i !== $index) {
        // If it is not the requested index don't build the elements.
        continue;
      }

      // The element delta is zero-based whereas the index is numerical and starts at 1.
      $delta = $i - 1;

      // Add the component element wrappers.
      $elements[$component->getPrefix()][$delta] = $component->getElementWrapper($i);

      // Generate the form elements for this index.
      $element = $component->getElements([], $i);

      // Handle instances where a redirect response is returned.
      if ($element instanceof RedirectResponse) {
        return $element;
      }

      // Add the component elements to the form array.
      $elements[$component->getPrefix()][$delta] = $element;

      // No actions are shown for form elements that support the summary list.
      $hide_element_actions = $this->supportsSummaryList($component) &&
        !$this->displaySummaryList($component, $index);

      // Add any element actions.
      if ($element_actions = $component->getElementActions($i) && !$hide_element_actions) {
        // Don't show the 'change' button when displaying form elements.
        unset($element_actions['change']);

        $elements[$component->getPrefix()][$delta]['actions'] = $element_actions;
      }
    }

    // Only show the 'add another' button for components that support multiple
    // items but don't use the summary list component.
    if ($component_actions = $component->getComponentActions() && !$this->supportsSummaryList($component)) {
      $elements[$component->getPrefix()]['actions'] = $component_actions;
    }

    return $elements;
  }

}
