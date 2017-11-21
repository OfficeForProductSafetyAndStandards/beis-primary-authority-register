<?php

namespace Drupal\par_actions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PAR Scheduler Rule plugin item annotation object.
 *
 * @see \Drupal\par_actions\ParScheduleManager
 * @see plugin_api
 *
 * @Annotation
 */
class ParSchedulerRule extends Plugin {


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
   * An associative array containing the optional key:
   *   - time: (optional) How much time Drupal cron should spend on calling
   *     this worker in seconds. Defaults to 15.
   *
   * @var array (optional)
   */
  public $cron;

  /**
   * The entity type to query.
   *
   * @var string
   */
  public $entity;

  /**
   * The entity time property to query.
   *
   * @var string
   */
  public $property;

  /**
   * The time relative to the entity/property.
   *
   * @var string
   */
  public $time;

  /**
   * Whether to resolve this action by sticking it in a queue
   * instead of trying to resolve immediately.
   *
   * @var boolean
   */
  public $queue;

  /**
   * The action to be performed on the entity.
   *
   * @var string
   */
  public $action;

}
