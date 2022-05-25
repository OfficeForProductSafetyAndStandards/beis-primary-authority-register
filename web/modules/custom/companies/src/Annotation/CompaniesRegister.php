<?php

namespace Drupal\companies\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a companies register plugin annotation object.
 *
 * @see \Drupal\companies\CompaniesManager
 * @see plugin_api
 *
 * @Annotation
 */
class CompaniesRegister extends Plugin {

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

}
