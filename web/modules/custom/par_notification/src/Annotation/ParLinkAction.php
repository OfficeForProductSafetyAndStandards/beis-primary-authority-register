<?php

namespace Drupal\par_notification\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PAR Notification Action plugin item annotation object.
 *
 * @see \Drupal\par_notification\ParLinkManager
 * @see plugin_api
 *
 * @Annotation
 */
class ParLinkAction extends Plugin {


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
   * The status of the plugin.
   *
   * This is an easy way to turn off plugins.
   *
   * @var bool
   */
  public $status = TRUE;

  /**
   * A list of notifications that this applies to.
   *
   * @var array
   */
  public $notification;

  /**
   * The destination path to redirect to after completion.
   *
   * @var string
   */
  public $destination;

  /**
   * The action to be performed on the entity.
   *
   * @var string
   */
  public $action;

}
