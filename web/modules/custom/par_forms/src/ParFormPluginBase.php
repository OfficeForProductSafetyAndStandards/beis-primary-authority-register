<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowNegotiatorInterface;

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
    return isset($this->formDefaults[$key]) ? $this->formDefaults[$key] : FALSE;
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
   * @return integer|NULL
   */
  public function countItems($data = NULL) {
    if ($this->getCardinality() !== 1) {
      return isset($data[ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId()]) ?
        count($data[ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId()]) :
        count($this->getFlowDataHandler()->getTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . $this->getPluginId()));
    }

    return NULL;
  }

  public function getDefaultValuesByKey($key, $cardinality, $default = '', $cid = NULL) {
    $element_key = $this->getElementKey($key, $cardinality);
    return $this->getFlowDataHandler()->getDefaultValues($this->getElementKey($key, $cardinality), $default, $cid);
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
   * @return string|array
   *   The key for this form element.
   */
  public function getElementName($element, $cardinality = 1) {

    $cardinality--;

    if ($this->getCardinality() !== 1 || $cardinality !== 1) {
      if (is_array($element)) {
        return ParFormBuilder::PAR_COMPONENT_PREFIX . "{$this->getPluginId()}[$cardinality][{implode('][',$element)}]";
      }
      else {
        return ParFormBuilder::PAR_COMPONENT_PREFIX . "{$this->getPluginId()}[$cardinality][$element]";
      }
    }
    else {
      return $element;
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
  public function validate(&$form_state, $cardinality = 1) {
    $violations = [];

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
          $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %entity_id: @detail.', ['%entity_id' => $entity->getEntityTypeId(), '@details' => $e->getMessage()]);
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
}
