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

  /**
   * Get's the mapping of each form element to the entity
   * property it will be eventually saved on.
   */
  public function getMapping();

  /**
   * Returns all the form elements for this form component.
   *
   * @param array $form
   *   An optional form array to add the component elements to.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   *
   * @return array|RedirectResponse
   */
  public function getElements($form = [], $cardinality = 0);

  public function getFormDefaults();

  public function getFormDefaultByKey($key);

  /**
   * Loads the data associated with these elements.
   *
   * @param array $form_state
   *   The form state object to validate.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   */
  public function loadData($cardinality = 0);

  /**
   * Validates the form elements.
   *
   * @param array $form
   *   The build form.
   * @param FormStateInterface $form_state
   *   The form state object to validate.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   * @param mixed $action
   *   An identifier relating to the action to be performed.
   */
  public function validate($form, &$form_state, $cardinality = 0, $action);

  /**
   * Saves the form elements.
   *
   * @param array $form_state
   *   The form state object to validate.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   */
  public function save($cardinality = 0);

}
