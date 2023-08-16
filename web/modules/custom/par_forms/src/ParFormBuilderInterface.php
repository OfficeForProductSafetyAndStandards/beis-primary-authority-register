<?php

namespace Drupal\par_forms;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines a form builder interface.
 *
 * @see plugin_api
 */
interface ParFormBuilderInterface {

  /**
   * Helper to load the data for the form component.
   *
   * @param ParFormPluginInterface $component
   *   The form plugin to load data for.
   */
  public function loadData(ParFormPluginInterface $component): void;

  /**
   * Build the form component.
   *
   * @param ParFormPluginInterface $component
   *   The form component plugin to build.
   *
   * @return array|RedirectResponse
   *   The form elements for the rendered component.
   */
  public function build(ParFormPluginInterface $component): array|RedirectResponse;

  /**
   * @param \Drupal\par_forms\ParFormPluginInterface $component
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param $cardinality
   *
   * @return mixed
   */
  public function validate(ParFormPluginInterface $component, $form, FormStateInterface &$form_state, $cardinality = NULL): void;

}
