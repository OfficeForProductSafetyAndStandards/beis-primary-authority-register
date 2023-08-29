<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
   * Returns all the form elements for this form component.
   *
   * @param array $form
   *   An optional form array to add the component elements to.
   * @param integer $index
   *   The cardinality for this plugin.
   *
   * @return array|RedirectResponse
   */
  public function getElements(array $form = [], int $index = 0);

  public function getFormDefaults(): array;

  public function getFormDefaultByKey($key);

  /**
   * Loads the data associated with these elements.
   *
   * @param integer $index
   *   The cardinality for this plugin.
   */
  public function loadData(int $index): void;

  /**
   * Validates the form elements.
   *
   * @param array $form
   *   The build form.
   * @param FormStateInterface $form_state
   *   The form state object to validate.
   * @param integer $index
   *   The cardinality for this plugin.
   * @param mixed $action
   *   An identifier relating to the action to be performed.
   */
  public function validate(array $form, FormStateInterface &$form_state, int $index = 0, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY);

  /**
   * Saves the form elements.
   *
   * @param integer $index
   *   The index for this plugin item to save.
   */
  public function save(int $index = 0);

}
