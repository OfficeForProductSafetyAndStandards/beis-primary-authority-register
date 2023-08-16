<?php

namespace Drupal\par_forms;

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
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowDataHandler;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_forms\Annotation\ParForm;

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
  protected $entityMapping = [];

  /**
   * Form defaults
   */
  protected $formDefaults = [];

  /**
   * Wrapper name used to identify this component to users.
   */
  protected $wrapperName = 'item';

  /**
   * {@inheritDoc}
   */
  public function getPluginNamespace() {
    return $this->getConfiguration()[ParFormPluginInterface::NAMESPACE_PROPERTY] ?? $this->getPluginId();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->getConfiguration()['weight'] ?: $this->pluginDefinition['weight'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCardinality() {
    return $this->getConfiguration()['cardinality'] ?: $this->pluginDefinition['cardinality'];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array_merge($this->defaultConfiguration(), $this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration = []) {
    $this->configuration = $configuration;
  }

  public function getFormDefaults() {
    return $this->formDefaults;
  }

  public function getFormDefaultByKey($key) {
    $defaults = $this->getFormDefaults();
    return $defaults[$key] ?? FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
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
  public function getFlowNegotiator() {
    return \Drupal::service('par_flows.negotiator');
  }

  /**
   * Simple getter to inject the flow data handler service.
   *
   * @return ParFlowDataHandlerInterface
   */
  public function getFlowDataHandler() {
    return \Drupal::service('par_flows.data_handler');
  }

  /**
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * Get unique pager service.
   *
   * @return \Drupal\unique_pager\UniquePagerService
   */
  public static function getUniquePager() {
    return \Drupal::service('unique_pager.unique_pager_service');
  }

  /**
   * Dynamically get url generator service.
   *
   * @return UrlGeneratorInterface
   */
  public function getUrlGenerator() {
    return \Drupal::service('url_generator');
  }

  /**
   * Dynamically get the entity field manager service.
   *
   * @return EntityFieldManagerInterface
   */
  public function getEntityFieldManager() {
    return \Drupal::service('entity_field.manager');
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Drupal\Core\Path\PathValidatorInterface
   */
  public function getPathValidator() {
    return \Drupal::service('path.validator');
  }

  /**
   * Return the date formatter service.
   *
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * Get the route to return to once the journey has been completed.
   */
  public function geFlowEntryURL() {
    // Get the route that we entered on.
    $entry_point = $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);
    try {
      $url = $this->getPathValidator()->getUrlIfValid($entry_point);
    }
    catch (\InvalidArgumentException $e) {

    }

    if ($url && $url instanceof Url && $url->isRouted()) {
      return $url;
    }
    return NULL;
  }

  /**
   * A Helper function to get the form data cache id for a given form in the flow.
   *
   * @param string $form_key
   *   The form data key or form id that maps to a given form id.
   *
   * @deprecated
   *
   * @return null|string
   */
  public function getFormCid($form_key) {
    return $this->getFlowNegotiator()->getFormKey($form_key);
  }

  /**
   * Identify whether this plugin instance supports multiple values.
   */
  public function isMultiple() {
    return $this->getCardinality() !== 1;
  }

  /**
   * Whether the data will be flattened when dealing with the form data.
   *
   * @return bool
   *   TRUE for single value components that don't maintain their form structure
   *   FALSE for multi value components that maintain their form structure with #tree
   */
  public function isFlattened() {
    return !$this->isMultiple();
  }

  /**
   * Identify whether another item can be added.
   *
   * @return bool
   *   TRUE if no more items can be added.
   *   FALSE if more items can be added.
   */
  public function isFull() {
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

    return $this->reindex($data);
  }

  /**
   * Get the plugin data for a given cardinality or instance.
   *
   * @param int $index
   *   The index of the item to get data for, not the cardinality of the item.
   *
   * @return ?array
   *   An array of data for a given plugin cardinality,
   *   or null if no data was found for the given cardinality.
   */
  public function getDataItem(int $index): ?array {
    $data = $this->getData();

    if ($this->isFlattened()) {
      // Flattened plugins only support a single cardinality.
      return $index === 1 ? $this->filterItem($data) : NULL;
    }
    else {
      return $data[$index] ?? NULL;
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
      $data = NestedArray::filter($data);
    }

    return $data;
  }

  public function filterItem(array $item) {
    // Unset data item level actions.
    unset($item['actions']);

    // If the data is flattened
    $item = NestedArray::filter($item);

    $defaults = $this->getFormDefaults();
    $item = array_filter($item, function ($value, $key) use ($defaults) {
      $default_value = $defaults[$key] ?? NULL;

      // If there is no default value
      if (!$default_value) {
        return TRUE;
      }

      return $default_value !== $value;
    }, ARRAY_FILTER_USE_BOTH);

    return $item;
  }

  /**
   * Re-index structured plugin data.
   */
  private function reindex($data) {
    // Do not re-index unstructured data.
    if ($this->isFlattened()) {
      return $data;
    }

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
  public function countItems($data = NULL) {
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
  public function getNewCardinality($data = NULL) {
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
   * @param $key
   *   The form data key.
   * @param $cardinality
   *   The cardinality to get the value for.
   * @param string $default
   *   The default value if none found.
   * @param null $cid
   *   The cache id.
   *
   * @return mixed|null
   */
  public function getDefaultValuesByKey($key, $cardinality, $default = '', $cid = NULL) {
    $element_key = $this->getElementKey($key, $cardinality);
    return $this->getFlowDataHandler()->getDefaultValues($element_key, $default, $cid);
  }

  /**
   * Set the defaults by a replacement form data key.
   *
   * @param $key
   *   The form data key.
   * @param $cardinality
   *   The cardinality to get the value for.
   * @param string $value
   *   The value to be set.
   *
   * @return mixed|null
   */
  public function setDefaultValuesByKey($key, $cardinality, $value = '') {
    $element_key = $this->getElementKey($key, $cardinality);
    return $this->getFlowDataHandler()->setFormPermValue($element_key, $value);
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
   */
  public function getPrefix() {
    return ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginNamespace();
  }

  /**
   * Gets the form key that metadata will be stored in.
   */
  public function getMetaDataKey() {
    return ['metadata', ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginNamespace()];
  }

  /**
   * Get the path to the element within the form, this is the full structured path.
   */
  public function getElementPath(array $element, int $cardinality): array {
    // The elements are based on a zero-based index, whereas the cardinality starts at 1.
    $index = $cardinality - 1;

    // Get the default plugin & cardinality prefix.
    $key = [$this->getPrefix(), $index];

    return array_merge($key, $element);
  }

  /**
   * Gets the data key for a given element, depends on the cardinality of this plugin.
   */
  public function getElementKey($element, $cardinality = 1, $force = FALSE) {
    // Get the full path to the element.
    $path = $this->getElementPath((array) $element, $cardinality);

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
   * Gets the element key depending on the cardinality of this plugin.
   *
   * @param array $form
   *   The form that contains the element.
   * @param mixed $element_path
   *   The path to the element within the plugin.
   * @param int $cardinality
   *   The cardinality of this element.
   *
   * @return ?array
   *   The form element.
   */
  public function getElement(array $form, mixed $element_path, int $cardinality): ?array {
    $path = $this->getElementPath($element_path, $cardinality);

    $key_exists = FALSE;
    $form_element = NestedArray::getValue($form, $path, $key_exists);

    return $key_exists ? $form_element : NULL;
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

  public function createMappingEntities() {
    $entities = [];

    foreach ($this->getMapping() as $entity_name => $form_items) {
      [$type, $bundle] = explode(':', $entity_name . ':');

      $entity_class = $this->getParDataManager()->getParEntityType($type)->getClass();
      // If the entity already exists as a data parameter use that.
      if ($entity = $this->getFlowDataHandler()->getParameter($type)) {
        $entities[$type] = $entity;
      }
      else {
        $entities[$type] = $entity_class::create([
          'type' => $this->getParDataManager()->getParBundleEntity($type, $bundle)->id(),
        ]);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    // @TODO Add automatic loading of data based on the mapping (self::getMapping)
    // between self::getElements() and self::getFlowDataHandler()->getParameters()
  }

  public function setData(&$params = []) {
    // @TODO Add automatic setting of data based on the mapping (self::getMapping)
    // between self::getElements() and self::getFlowDataHandler()->getParameters()
  }

  /**
   * An #after_build callback to set options descriptions for
   * elements that support #options such as checkboxes and radios.
   */
  public static function optionsDescriptions(array $element, FormStateInterface $form_state) {
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
  public function validate($form, &$form_state, $cardinality = 1, $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    foreach ($this->createMappedEntities() as $entity) {
      if (!$this->isFlattened()) {
        $prefix = [$this->getPrefix(), $cardinality];
        $values = $form_state->getValue($prefix);
      }
      else {
        $values = $form_state->getValues();
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
                $element = $this->getElement($form, $mapping->getElement(), $cardinality);
                $this->setError($form, $form_state, $element, $violation->getMessage());
              }
              catch (ParFlowException $ignore) {
                // The element could not be found.
              }

              break;

            case ParFormBuilder::PAR_ERROR_CLEAR:
              // If the plugin contains structured data erase just the plugin cardinality.
              if (!$this->isFlattened()) {
                $prefix = [$this->getPrefix(), $cardinality];
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
  public function save($cardinality = 1) {
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
          '#name' => "change:{$this->getPluginId()}:{$index}",
          '#weight' => 100,
          '#submit' => ['::changeItem'],
          '#value' => $this->t("Change"),
          '#attributes' => [
            'class' => ['btn-link'],
            'aria-label' => "Change {$this->getWrapperName()} $index",
            'data-prevent-double-click' => 'true',
            'data-module' => 'govuk-button',
          ],
        ];
      }

      $actions['remove'] = [
        '#type' => 'submit',
        '#name' => "remove:{$this->getPluginId()}:{$index}",
        '#weight' => 100,
        '#submit' => ['::removeItem'],
        '#value' => $this->t("Remove"),
        '#attributes' => [
          'class' => ['btn-link'],
          'aria-label' => "Remove {$this->getWrapperName()} $index",
          'data-prevent-double-click' => 'true',
          'data-module' => 'govuk-button',
        ],
      ];
    }

    if (!empty($actions)) {
      $actions['#type'] = 'actions';
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
          'class' => ['btn-link'],
          'data-prevent-double-click' => 'true',
          'data-module' => 'govuk-button',
        ],
      ];
    }

    if (!empty($actions)) {
      $actions['#type'] = 'actions';
    }
    else {
      return NULL;
    }
  }
}
