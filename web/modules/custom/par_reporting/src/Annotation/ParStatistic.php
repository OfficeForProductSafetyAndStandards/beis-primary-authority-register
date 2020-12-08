<?php

namespace Drupal\par_reporting\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PAR Reporting plugin item annotation object.
 *
 * @see \Drupal\par_reporting\ParReportingManager
 * @see plugin_api
 *
 * @Annotation
 */
class ParStatistic extends Plugin {

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
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

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
   * A timestamp dictating how quickly this statistic can become stale.
   *
   * @var int
   */
  public $staleness;

}
