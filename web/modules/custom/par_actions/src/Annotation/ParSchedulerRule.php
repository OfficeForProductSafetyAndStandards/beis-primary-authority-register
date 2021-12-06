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
   * These time properties should be recorded in relative time formats:
   * https://www.php.net/manual/en/datetime.formats.relative.php
   *
   * e.g. "+5 weeks", "1 day", "-12 days", "-7 weekdays", "-1 year"
   *
   * A custom relative time format "+5 working days" is also supported.
   *
   * "+ X units" means X many units _before_ the entity's date.
   * "- X units" means X many units _after_ the entity's date.
   *
   * Non-number based time formats are also supported but may have unexpected
   * results because relative times that increase the date "first day of next month"
   * will fire _before_ the entity date, and times that decrease the date
   * "2 days ago" will fire _after_ the entity date. As a result these are best
   * avoided, for example "yesterday" is _not_ equivalent to "-1 day".
   *
   * @var string
   */
  public $time;

  /**
   * The frequency with which this rule can run each action.
   *
   * These time properties should be recorded in relative time formats,
   * limited to days, weeks, months and years, and are assumed that
   * these are always in the future:
   * https://www.php.net/manual/en/datetime.formats.relative.php
   *
   * e.g. "1 day", "2 weeks", "3 months", "1 year"
   *
   * For example if a rule is allowed to run with a frequency of "1 month"
   * then each action will only per run once per entity every month.
   *
   * The default is "1 week".
   *
   * @var string
   */
  public $frequency;

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
