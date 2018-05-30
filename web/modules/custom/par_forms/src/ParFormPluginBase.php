<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_flows\ParRedirectTrait;

/**
 * Provides a base implementation for a ParForm plugin.
 *
 * @see \Drupal\par_forms\ParScheduleInterface
 * @see \Drupal\par_forms\ParScheduleManager
 * @see \Drupal\par_forms\Annotation\ParSchedulerRule
 * @see plugin_api
 */
abstract class ParFormPluginBase extends PluginBase implements ParFormPluginBaseInterface {

  use StringTranslationTrait;
  use ParRedirectTrait;
  use ParDisplayTrait;
  use LoggerChannelTrait;

  /**
   * A mapping definition of form elements to entity properties.
   */
  protected $formItems = [];

  /**
   * Form defaults
   */
  protected $formDefaults = [];

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
    return isset($defaults[$key]) ? $defaults[$key] : FALSE;
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
   * A Helper function to get the form data cache id for a given form in the flow.
   *
   * @param string $form_data_key
   *   The form data key that maps to a given form id.
   * @return null|string
   */
  public function getFormCid($form_data_key) {
    $form_data_keys = $this->getFlowNegotiator()->getFlow()->getCurrentStepFormDataKeys();
    $form_id = isset($form_data_keys[$form_data_key]) ? $form_data_keys[$form_data_key] : NULL;

    return $form_id ? $this->getFlowNegotiator()->getFormKey($form_id) : NULL;
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
    if ($this->getCardinality() !== 1) {
      return isset($data[ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId()]) ?
        count($data[ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId()]) :
        count($this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId()));
    }

    return 0;
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
   * Get the defaults by a replacement form data key.
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
   * Get's the element key depending on the cardinality of this plugin.
   *
   * @param $element
   *   The element key.
   * @param int $cardinality
   *   The cardinality of this element.
   *
   * @return string|array
   *   The key for this form element.
   */
  public function getElementKey($element, $cardinality = 1) {
    if ($this->getCardinality() !== 1 || $cardinality !== 1) {
      $key = [ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId(), $cardinality-1];
      if (is_array($element)) {
        foreach ($element as $e) {
          array_push($key, $e);
        }
        return $key;
      }
      else {
        array_push($key, $element);
        return $key;
      }
    }
    else {
      return $element;
    }
  }

  /**
   * Get's the element name depending on the cardinality of this plugin.
   *
   * @param $element
   *   The element key.
   * @param int $cardinality
   *   The cardinality of this element.
   *
   * @return string
   *   The key for this form element.
   */
  public function getElementName($element, $cardinality = 1) {
    if ($this->getCardinality() !== 1 || $cardinality !== 1) {
      $index = $cardinality-1;
      if (is_array($element)) {
        $elements = implode('][', $element);
        return ParFormBuilder::PAR_COMPONENT_PREFIX . "{$this->getPluginId()}[$index][$elements]";
      }
      else {
        return ParFormBuilder::PAR_COMPONENT_PREFIX . "{$this->getPluginId()}[$index][$element]";
      }
    }
    else {
      if (is_array($element)) {
        return implode('][', $element) . ']';
      }
      else {
        return $element;
      }
    }
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
      list($type, $bundle) = explode(':', $entity_name . ':');

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
   * {@inheritdoc}
   */
  public function validate(&$form_state, $cardinality = 1, array $violations = []) {
    // Assign all the form values to the relevant entity field values.
    foreach ($this->getMapping() as $entity_name => $form_items) {
      list($type, $bundle) = explode(':', $entity_name . ':');

      $entity_class = $this->getParDataManager()->getParEntityType($type)->getClass();
      $entity = $entity_class::create([
        'type' => $this->getParDataManager()->getParBundleEntity($type, $bundle)->id(),
      ]);

      foreach ($form_items as $field_name => $form_item) {
        $field_definition = $this->getParDataManager()->getFieldDefinition($entity->getEntityTypeId(), $entity->bundle(), $field_name);

        if (is_array($form_item)) {
          $field_value = [];
          foreach ($form_item as $field_property => $form_property_item) {
            // For entity reference fields we need to transform the ids to integers.
            if ($field_definition->getType() === 'entity_reference' && $field_property === 'target_id') {
              $field_value[$field_property] = (int) $form_state->getValue($this->getElementKey($form_property_item, $cardinality));
            }
            else {
              $field_value[$field_property] = $form_state->getValue($this->getElementKey($form_property_item, $cardinality));
            }
          }
        }
        else {
          $field_value = $form_state->getValue($this->getElementKey($form_item, $cardinality));
        }

        $entity->set($field_name, $field_value);

        try {
          $violations[$field_name] = $entity->validate()->filterByFieldAccess()->getByFields([$field_name]);
        }
        catch(\Exception $e) {
          $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %entity_id: @details.', ['%entity_id' => $entity->getEntityTypeId(), '@details' => $e->getMessage()]);
        }
      }
    }

    return $violations;
  }

  /**
   * {@inheritdoc}
   */
  public function save($cardinality = 1) {
    // @TODO Add automatic saving of data based on the mapping (self::getMapping)
    // between self::getElements() and self::getFlowDataHandler()->getParameters()
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getWrapper() {
    return [
      '#weight' => $this->getWeight(),
      '#tree' => $this->getCardinality() === 1 ? FALSE : TRUE,
    ];
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    $count = $this->getNewCardinality();

    if ($this->getCardinality() !== 1 && $cardinality !== $count) {
      $actions['remove'] = [
          '#type' => 'submit',
          '#name' => "remove:{$this->getPluginId()}:{$cardinality}",
          '#weight' => 100,
          '#submit' => ['::removeItem'],
          '#value' => $this->t("Remove"),
          '#attributes' => [
            'class' => ['btn-link'],
          ],
      ];
    }

    return $actions;
  }

  /**
   * Get the fieldset wrapper for this component.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    $count = isset($count) ? $count : $this->getNewCardinality();

    if ($this->getCardinality() === -1
      || ($this->getCardinality() > 1 && $this->getCardinality() > $count)) {
      $actions['add_another'] = [
        '#type' => 'submit',
        '#name' => 'add_another',
        '#submit' => ['::multipleItemActionsSubmit'],
        '#value' => $this->t('Add another'),
        '#attributes' => [
          'class' => ['btn-link'],
        ],
      ];
    }

    return $actions;
  }
}
