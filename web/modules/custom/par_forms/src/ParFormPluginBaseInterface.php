<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines a form component for use within a form controller.
 *
 * @see plugin_api
 */
interface ParFormPluginBaseInterface extends PluginInspectionInterface {

  /**
   * Returns all the form elements for this form component.
   */
  public function getElements();

  /**
   * Get's the mapping of each element to the entity property it will be eventually saved.
   */
  public function getMapping();

  /**
   * Validates the form elements.
   */
  public function validate();

}
