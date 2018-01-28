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
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->pluginDefinition['weight'];
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
  public function getMapping() {
    return $this->formItems;
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // @TODO Add automatic loading of data based on the mapping (self::getMapping)
    // between self::getElements() and self::getFlowDataHandler()->getParameters()
  }

  /**
   * {@inheritdoc}
   */
  public function validate(&$form_state) {
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
              $field_value[$field_property] = (int) $form_state->getValue($form_property_item);
            }
            else {
              $field_value[$field_property] = $form_state->getValue($form_property_item);
            }
          }
        }
        else {
          $field_value = $form_state->getValue($form_item);
        }

        $entity->set($field_name, $field_value);

        try {
          drupal_set_message($form_item);
          $violations[$form_item] = $entity->validate()->filterByFieldAccess()
            ->getByFields([
              $field_name,
            ]);
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
  public function save() {
    // @TODO Add automatic saving of data based on the mapping (self::getMapping)
    // between self::getElements() and self::getFlowDataHandler()->getParameters()
  }
}
