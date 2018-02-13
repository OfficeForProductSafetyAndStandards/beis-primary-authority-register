<?php

namespace Drupal\par_forms\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PAR Form plugin item annotation object.
 *
 * @see \Drupal\par_forms\ParFormBuilder
 * @see plugin_api
 *
 * @Annotation
 */
class ParForm extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The plugin weight.
   *
   * The higher the weight the later in the filter order the plugin will be.
   *
   * @var int
   */
  public $weight = 0;

  /**
   * The plugin cardinality.
   *
   * The number of times the plugin can be added.
   *
   * @var int
   */
  public $cardinality = 1;

}
