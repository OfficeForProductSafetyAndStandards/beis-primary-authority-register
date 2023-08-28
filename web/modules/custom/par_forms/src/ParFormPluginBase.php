<?php

namespace Drupal\par_forms;

use Drupal;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowDataHandler;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\unique_pager\UniquePagerService;
use Drupal\Core\Path\PathValidatorInterface;
use InvalidArgumentException;

/**
 * Provides a base implementation for a ParForm plugin.
 *
 * @see \Drupal\par_forms\ParScheduleInterface
 * @see \Drupal\par_forms\ParScheduleManager
 * @see \Drupal\par_forms\Annotation\ParSchedulerRule
 * @see plugin_api
 */
abstract class ParFormPluginBase extends PluginBase implements ParFormPluginInterface {

  use StringTranslationTrait;
  use ParRedirectTrait;
  use ParDisplayTrait;
  use LoggerChannelTrait;
  use ParEntityValidationMappingTrait;

  /**
   * The mapping definitions to validate against.
   *
   * A list of arguments passed to the ParEntityMapping constructor
   *
   * @see ParEntityMapping
   */
  protected array $entityMapping = [];

  /**
   * Form defaults.
   */
  protected array $formDefaults = [];

  /**
   * Wrapper name used to identify this component to users.
   */
  protected string $wrapperName = 'item';

  /**
   * {@inheritDoc}
   */
  public function getPluginNamespace(): string {
    return $this->getConfiguration()[ParFormPluginInterface::NAMESPACE_PROPERTY] ?? $this->getPluginId();
  }

  /**
   * Get the title for the plugin.
   */
  public function getTitle() {
    return $this->pluginDefinition['label'];
  }

  /**
   * Get the plugin weight.
   */
  public function getWeight() {
    return $this->getConfiguration()['weight'] ?: $this->pluginDefinition['weight'];
  }

  /**
   * Get the cardinality for this plugin instance.
   *
   */
  public function getCardinality() {
    return $this->getConfiguration()['cardinality'] ?: $this->pluginDefinition['cardinality'];
  }

  /**
   * Get any additional plugin configuration elements.
   *
   * @return array
   */
  public function getConfiguration(): array {
    return array_merge($this->defaultConfiguration(), $this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration = []): void {
    $this->configuration = $configuration;
  }

  /**
   * Get any default values that may be set.
   *
   * @return array
   */
  public function getFormDefaults(): array {
    return $this->formDefaults;
  }

  public function getFormDefaultByKey($key) {
    $defaults = $this->getFormDefaults();
    return $defaults[$key] ?? FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'weight' => 0,
      'cardinality' => 1,
    ];
  }


  /**
   * Simple getter to inject the flow negotiator service.
   *
   * @return ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator(): ParFlowNegotiatorInterface {
    return Drupal::service('par_flows.negotiator');
  }

  /**
   * Simple getter to inject the flow data handler service.
   *
   * @return ParFlowDataHandlerInterface
   */
  public function getFlowDataHandler(): ParFlowDataHandlerInterface {
    return Drupal::service('par_flows.data_handler');
  }

  /**
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager(): ParDataManagerInterface {
    return Drupal::service('par_data.manager');
  }

  /**
   * Get unique pager service.
   *
   * @return UniquePagerService
   */
  public static function getUniquePager(): UniquePagerService {
    return Drupal::service('unique_pager.unique_pager_service');
  }

  /**
   * Dynamically get url generator service.
   *
   * @return UrlGeneratorInterface
   */
  public function getUrlGenerator(): UrlGeneratorInterface {
    return Drupal::service('url_generator');
  }

  /**
   * Dynamically get the entity field manager service.
   *
   * @return EntityFieldManagerInterface
   */
  public function getEntityFieldManager(): EntityFieldManagerInterface {
    return Drupal::service('entity_field.manager');
  }

  /**
   * Get the event dispatcher service.
   *
   * @return PathValidatorInterface
   */
  public function getPathValidator(): PathValidatorInterface {
    return Drupal::service('path.validator');
  }

  /**
   * Return the date formatter service.
   *
   * @return DateFormatterInterface
   */
  protected function getDateFormatter(): DateFormatterInterface {
    return Drupal::service('date.formatter');
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel(): string {
    return 'par';
  }

  /**
   * Calculate the dependencies.
   */
  public function calculateDependencies(): array {
    return [];
  }

  /**
   * Get the route to return to once the journey has been completed.
   *
   * @return ?Url
   */
  public function geFlowEntryURL(): ?Url {
    // Get the route that we entered on.
    $entry_point = $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);
    try {
      $url = $this->getPathValidator()->getUrlIfValid($entry_point);
    }
    catch (InvalidArgumentException $ignored) {

    }

    if (isset($url) && $url instanceof Url && $url->isRouted()) {
      return $url;
    }
    return NULL;
  }

  /**
   * Identify whether this plugin instance supports multiple values.
   *
   * @return bool
   *   TRUE if the component instance supports more than 1 item
   *   FALSE if the component instance only allows a single item value
   */
  public function isMultiple(): bool {
    return $this->getCardinality() !== 1;
  }

  /**
   * Whether the data will be flattened when dealing with the form data.
   *
   * @return bool
   *   TRUE for single value components that don't maintain their form structure
   *   FALSE for multi value components that maintain their form structure with #tree
   */
  public function isFlattened(): bool {
    return !$this->isMultiple();
  }

  /**
   * Identify whether the plugin has already added all the available items
   * for this component instance or whether another item can be added.
   *
   * @return bool
   *   TRUE if no more items can be added.
   *   FALSE if more items can be added.
   */
  public function isFull(): bool {
    return $this->getCardinality() !== FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED &&
      $this->countItems() >= $this->getCardinality();
  }

  /**
   * Get the plugin data.
   *
   * @return array
   *   An array of data,
   *   or an empty array if no data was found.
   */
  public function getData(): array {
    // Ensure that at the very
    $data = (array) $this->getFlowDataHandler()->getPluginTempData($this);

    // Allow plugins to filter
    $data = $this->filter($data);

    // Always get the data in the correct order.
    return $this->reindex($data);
  }

  /**
   * Set the plugin data.
   *
   * @param array $data
   *   The plugin data to store.
   */
  public function setData(array $data): void {
    // Allow plugins to filter
    $data = $this->filter($data);

    // Always reindex the data before saving.
    $data = $this->reindex($data);

    $this->getFlowDataHandler()->setPluginTempData($this, $data);
  }

  /**
   * Get the plugin data for a given cardinality or instance.
   *
   * @param int $delta
   *   The delta of the item to get data for, not the index of the item.
   *
   * @return ?array
   *   An array of data for a given plugin cardinality,
   *   or null if no data was found for the given cardinality.
   */
  public function getDataItem(int $delta): ?array {
    $data = $this->getData();

    if ($this->isFlattened()) {
      // Flattened plugins only support a single cardinality.
      return $delta === 0 ? $data : NULL;
    }
    else {
      return $data[$delta] ?? NULL;
    }
  }

  /**
   * Filter empty data.
   *
   * Allows a plugin to filter out incomplete data values, so that only
   * a complete set of plugin data is submitted.
   *
   * @example radio elements that are selected and can't be unselected don't
   * necessarily form a complete dataset.
   *
   * In most cases the form validation ensures that plugin data must be completed,
   * but there are other submission handlers that don't validate data and require
   * any incomplete plugin datasets to be discarded (most form actions that submit
   * the form for processing but don't progress the journey don't require validation).
   *
   * Individual plugin instances can extend this behaviour.
   *
   * @param array $data
   *   An array of structured or unstructured data relative to the plugin
   *   (excluding the plugin prefix).
   *
   * @return array
   *   The filtered data.
   */
  public function filter(array $data): array {
    // Unset component level actions.
    unset($data['actions']);

    // Handle structured and unstructured data.
    if ($this->isFlattened()) {
      return $this->filterItem($data);
    }
    else {
      // Apply filtering to each instance cardinality.
      foreach ($data as $index => $row) {
        $data[$index] = $this->filterItem($row);
      }

      // Ensure that empty plugin instances are ignored.
      $data = $this->getFlowDataHandler()->filter($data);
    }

    return $data;
  }

  /**
   * Filter a single item value  for the given plugin.
   *
   * @param array $item
   *
   * @return array
   *   A filtered data item.
   */
  public function filterItem(array $item): array {
    // Unset data item level actions.
    unset($item['actions']);

    // If the data is flattened
    $item = $this->getFlowDataHandler()->filter($item);

    $defaults = $this->getFormDefaults();
    return array_filter($item, function ($value, $key) use ($defaults) {
      $default_value = $defaults[$key] ?? NULL;

      // If there is no default value
      if (!$default_value) {
        return TRUE;
      }

      return $default_value !== $value;
    }, ARRAY_FILTER_USE_BOTH);
  }

  /**
   * Re-index structured plugin data.
   *
   * @return array
   *   The items re-indexed in a list.
   */
  private function reindex($data): array {
    // Do not re-index unstructured data.
    if ($this->isFlattened()) {
      return $data;
    }

    // Sort the data so that array values are always returned in the same order.
    ksort($data, SORT_NUMERIC);

    // Return the re-indexed data.
    return array_values($data);
  }

  /**
   * Count the cardinality of already submitted values.
   *
   * @param mixed $data
   *   If required the data to be counted can be switched to the form_state values.
   *
   * @return integer
   */
  public function countItems(array $data = NULL): int{
    // If data is not passed attempt to retrieve it from the data handler.
    if (empty($data)) {
      $data = $this->getData();
    }

    // If the plugin values are flattened it only supports single values.
    if ($this->isFlattened()) {
      return !empty($data) ? 1 : 0;
    }
    // Otherwise count the data values.
    else {
      return !empty($data) ? count($data) : 0;
    }
  }

  /**
   * Get the next available cardinality for adding a new item.
   *
   * @param mixed $data
   *   If required the data to be counted can be switched to the form_state values.
   *
   * @return integer
   */
  public function getNewCardinality(array $data = NULL): int {
    if (!$data) {
      $data = $this->getData();
    }

    $data = $this->reindex($data);

    $count = $this->countItems($data);

    // If there is no add another button don't display an empty item.
    $actions = $this->getComponentActions([], $count);
    if ($actions && isset($actions['add_another'])) {
      $count++;
    }

    return $count ?: 1;
  }

  /**
   * Get the defaults by a replacement form data key.
   *
   * @param mixed $key
   *   The form data key.
   * @param $index
   *   The index to get the value for.
   * @param mixed $default
   *   The default value if none found.
   * @param ?string $cid
   *   The cache id.
   *
   * @return ?mixed
   */
  public function getDefaultValuesByKey(mixed $key, int $index, mixed $default = '', $cid = NULL): mixed {
    $element_key = $this->getElementKey($key, $index);
    return $this->getFlowDataHandler()->getDefaultValues($element_key, $default, $cid);
  }

  /**
   * Set the defaults by a replacement form data key.
   *
   * @param $key
   *   The form data key.
   * @param $index
   *   The index to set the value for.
   * @param mixed $value
   *   The value to be set.
   *
   * @return mixed|null
   */
  public function setDefaultValuesByKey(mixed $key, int $index = 1, mixed $value = ''): void {
    $element_key = $this->getElementKey($key, $index);
    $this->getFlowDataHandler()->setFormPermValue($element_key, $value);
  }

  /**
   * Set the form error.
   *
   * @param array $form
   *   The complete form array.
   * @param FormStateInterface $form_state
   *   The form state.
   * @param array $path
   *   The full path to the element.
   * @param string $message
   *   The error message to display.
   */
  protected function setError(array $form, FormStateInterface &$form_state, array $element, string $message) {
    // Link the message to the element.
    $id = $element['#id'] ?? NULL;
    $message = $this->wrapErrorMessage($message, $id);

    $form_state->setError($element, $message);
  }

  /**
   * Gets the element key prefix for multiple cardinality forms.
   *
   * @return string
   */
  public function getPrefix(): string {
    return ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginNamespace();
  }

  /**
   * Gets the form key that metadata will be stored in.
   */
  public function getMetaDataKey() {
    return ['metadata', ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginNamespace()];
  }

  /**
   * Gets the element key depending on the cardinality of this plugin.
   *
   * @param array $form
   *   The form that contains the element.
   * @param mixed $element_path
   *   The path to the element within the plugin.
   * @param int $index
   *   The index of this element.
   *
   * @return ?array
   *   The form element.
   */
  public function getElement(array $form, mixed $element_path, int $index): ?array {
    $path = $this->getElementPath($element_path, $index);

    $key_exists = FALSE;
    $form_element = NestedArray::getValue($form, $path, $key_exists);

    return $key_exists ? $form_element : NULL;
  }

  /**
   * Get the path to the element within the form, this is the full structured path.
   */
  public function getElementPath(mixed $element, int $cardinality): array {
    // The elements are based on a zero-based index, whereas the cardinality starts at 1.
    $index = $cardinality - 1;

    // Get the default plugin & cardinality prefix.
    $key = [$this->getPrefix(), $index];

    return array_merge($key, (array) $element);
  }

  /**
   * Gets the data key for a given element, depends on the cardinality of this plugin.
   */
  public function getElementKey(mixed $element, int $index = 1, $force = FALSE) {
    // Get the full path to the element.
    $path = $this->getElementPath((array) $element, $index);

    return $this->getItemKey($path);
  }

  /**
   * Gets the data key for a given element within an individual instance of a data item.
   *
   * @param array $element
   *   The element to get the key for.
   *
   * @return array
   *   An array representing the data key for the element within any
   *   given instance of a plugin data item.
   */
  public function getItemKey(array $element): array {
    // If the plugin data is structured return the full path to the element,
    // otherwise return the last element representing the flattened path.
    return !$this->isFlattened() ?
      $element :
      (array) end($element);
  }

  /**
   * {@inheritdoc}
   */
  public function getMapping() {
    return $this->formItems;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaults() {
    return $this->defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    // To be overridden by individual plugins.
  }

  /**
   * An #after_build callback to set options descriptions for
   * elements that support #options such as checkboxes and radios.
   *
   * This method must be applied as an #after_build callback to any elements that need it.
   * @code
   * '#options' => ['First', 'Second', 'Third'],
   * '#options_descriptions' => ['The first element', 'The second element', 'The third element'],
   * '#after_build' => [ [$this, 'optionsDescriptions'] ],
   * @endcode
   */
  public static function optionsDescriptions(array $element, FormStateInterface $form_state) {
    // Only act on input elements that have both the #options and #options_descriptions keys.
    if (!$element['#options'] || !$element['#options_descriptions']) {
      return $element;
    }

    foreach (Element::children($element) as $key) {
      if (isset($element['#options_descriptions'][$key])) {
        $element[$key]['#description'] = $element['#options_descriptions'][$key];
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    foreach ($this->createMappedEntities() as $entity) {
      if (!$this->isFlattened()) {
        $delta = $index -1;
        $prefix = [$this->getPrefix(), $delta];
        $values = $form_state->cleanValues()->getValue($prefix);
      }
      else {
        $values = $form_state->cleanValues()->getValues();
      }

      // If there are no values don't validate this cardinality.
      // This can happen for newly added cardinality blocks.
      if (empty($values)) {
        continue;
      }
      $this->buildEntity($entity, $values);

      // Validate the built entities by field only.
      $violations = [];
      try {
        $field_names = $this->getFieldNamesByEntityType($entity->getEntityTypeId());
        $violations = $entity->validate()->filterByFieldAccess()->getByFields($field_names);
      }
      catch(\Exception $e) {
        $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %entity_id: @details.', ['%entity_id' => $entity->getEntityTypeId(), '@details' => $e->getMessage()]);
      }

      // For each violation set the correct error message, clear the values or ignore validation.
      foreach ($violations as $violation) {
        if ($mapping = $this->getElementByViolation($violation)) {
          switch ($action) {
            case ParFormBuilder::PAR_ERROR_DISPLAY:
              try {
                $element = $this->getElement($form, $mapping->getElement(), $index);
                $message = $mapping->getErrorMessage($violation->getMessage());
                $this->setError($form, $form_state, $element, $message);
              }
              catch (ParFlowException $ignore) {
                // The element could not be found.
              }

              break;

            case ParFormBuilder::PAR_ERROR_CLEAR:
              // If the plugin contains structured data erase just the plugin cardinality.
              if (!$this->isFlattened()) {
                $prefix = [$this->getPrefix(), $index];
                $form_state->unsetValue($prefix);
              }
              // Otherwise erase all data within the submitted form.
              else {
                foreach ($form_state->getValues() as $key => $value) {
                  // @TODO this seems a bit brutal and will erase data set by
                  // other single cardinality forms on that page, there should
                  // be a better way to erase just the data set by this plugin.
                  $form_state->unsetValue($key);
                }
              }

              break;

          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save($index = 1) {
    // @see ParEntityValidationMappingTrait::buildEntity to build and save the values to an entity.
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getWrapper() {
    // If this form component supports multiple items then #tree will be set
    // on the wrapper and the data returned will not be flattened on submission.
    return [
      '#type' => 'container',
      '#weight' => $this->getWeight(),
      '#tree' => $this->isMultiple(),
      '#attributes' => ['class' => [Html::cleanCssIdentifier('component-' . $this->getPluginId())]]
    ];
  }

  /**
   * Get the wrapper name.
   */
  public function getWrapperName() {
    return $this->wrapperName;
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getElementWrapper($cardinality = 1) {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['component-item', Html::cleanCssIdentifier('component-item-' . $cardinality)]]
    ];
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getElementActions($index = 1, $actions = []) {
    // The delta value for the data is a zero-based index.
    $delta = $index - 1;

    // Items can only be removed if the cardinality supports multiple values,
    // and data has already been submitted for this index.
    if ($this->isMultiple() && $this->getDataItem($delta)) {
      // Items can only be changed if the plugin supports the Summary List format.
      if ($this->isMultiple() && $this instanceof ParSummaryListInterface) {
        $actions['change'] = [
          '#type' => 'submit',
          '#name' => "change:{$this->getPluginId()}:$index",
          '#weight' => 100,
          '#value' => $this->t("Change"),
          '#submit' => ['::changeItem'],
          '#validate' => ['::validateCancelForm'],
          '#limit_validation_errors' => [],
          '#attributes' => [
            'class' => ['btn-link', 'change-action'],
            'aria-label' => "Change {$this->getWrapperName()} $index",
            'data-prevent-double-click' => 'true',
            'data-module' => 'govuk-button',
          ],
        ];
      }

      $actions['remove'] = [
        '#type' => 'submit',
        '#name' => "remove:{$this->getPluginId()}:$index",
        '#weight' => 100,
        '#value' => $this->t("Remove"),
        '#submit' => ['::removeItem'],
        '#validate' => ['::validateCancelForm'],
        '#limit_validation_errors' => [],
        '#attributes' => [
          'class' => ['btn-link', 'remove-action'],
          'aria-label' => "Remove {$this->getWrapperName()} $index",
          'data-prevent-double-click' => 'true',
          'data-module' => 'govuk-button',
        ],
      ];
    }

    if (!empty($actions)) {
      $actions['#type'] = 'actions';
      return $actions;
    }
    else {
      return NULL;
    }
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    if ($this->isMultiple() && !$this->isFull()) {
      $actions['add_another'] = [
        '#type' => 'submit',
        '#name' => 'add_another',
        '#submit' => ['::addAnother'],
        '#value' => $this->t('Add another'),
        '#attributes' => [
          'class' => ['btn-link', 'add-action'],
          'data-prevent-double-click' => 'true',
          'data-module' => 'govuk-button',
        ],
      ];
    }

    if (!empty($actions)) {
      $actions['#type'] = 'actions';
      return $actions;
    }
    else {
      return NULL;
    }
  }
}
