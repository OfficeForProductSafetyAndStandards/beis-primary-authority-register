<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form component for use within a form controller.
 *
 * @see plugin_api
 */
interface ParFormPluginInterface extends PluginInspectionInterface, ConfigurableInterface {

  /**
   * Define the name property.
   */
  const NAME_PROPERTY = 'plugin_name';
  const NAMESPACE_PROPERTY = 'plugin_namespace';

  /**
   * Get the plugin namespace.
   *
   * The namespace allows each instance of a plugin to be referred to by a
   * different moniker.
   *
   * @return string
   *   The plugin namespace.
   */
  public function getPluginNamespace(): string;

  /**
   * Gets the mapping of each form element to the entity
   * property it will be eventually saved on.
   */
  public function getMapping();

  /**
   * Identify whether this plugin instance supports multiple values.
   *
   * @return bool
   *   TRUE if the component instance supports more than 1 item
   *   FALSE if the component instance only allows a single item value
   */
  public function isMultiple(): bool;

  /**
   * Whether the data will be flattened when dealing with the form data.
   *
   * @return bool
   *   TRUE for single value components that don't maintain their form structure
   *   FALSE for multi value components that maintain their form structure with #tree
   */
  public function isFlattened(): bool;

  /**
   * Identify whether the plugin has already added all the available items
   * for this component instance or whether another item can be added.
   *
   * @param mixed $data
   *   If required the data to be counted can be switched to the form_state values.
   *
   * @return bool
   *   TRUE if no more items can be added.
   *   FALSE if more items can be added.
   */
  public function isFull(array $data = NULL): bool;

  /**
   * Count the cardinality of already submitted values.
   *
   * @param mixed $data
   *   If required the data to be counted can be switched to the form_state values.
   *
   * @return int
   */
  public function countItems(array $data = NULL): int;

  /**
   * Whether the plugin has any data.
   *
   * @return bool
   */
  public function hasData(): bool;

  /**
   * Get the plugin data.
   *
   * @return array
   *   An array of data,
   *   or an empty array if no data was found.
   */
  public function getData(): array;

  /**
   * Set the plugin data.
   *
   * @param array $data
   *   The plugin data to store.
   */
  public function setData(array $data): void;

  /**
   * Returns all the form elements for this form component.
   *
   * @param array $form
   *   An optional form array to add the component elements to.
   * @param int $index
   *   The cardinality for this plugin.
   *
   * @return array|RedirectResponse
   */
  public function getElements(array $form = [], int $index = 0);

  /**
   *
   */
  public function getFormDefaults(): array;

  /**
   *
   */
  public function getFormDefaultByKey($key);

  /**
   * Loads the data associated with these elements.
   *
   * @param int $index
   *   The cardinality for this plugin.
   */
  public function loadData(int $index): void;

  /**
   * Validates the form elements.
   *
   * @param array $form
   *   The build form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object to validate.
   * @param int $index
   *   The cardinality for this plugin.
   * @param mixed $action
   *   An identifier relating to the action to be performed.
   */
  public function validate(array $form, FormStateInterface &$form_state, int $index = 0, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY);

  /**
   * Saves the form elements.
   *
   * @param int $index
   *   The index for this plugin item to save.
   */
  public function save(int $index = 0);

}
