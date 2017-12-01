<?php

namespace Drupal\par_notification\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PAR Notification plugin item annotation object.
 *
 * @see \Drupal\par_notification\ParNotifier
 * @see plugin_api
 *
 * @Annotation
 */
class ParNotifier extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The administrative label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
