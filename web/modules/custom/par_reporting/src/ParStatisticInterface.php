<?php

namespace Drupal\par_reporting;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for a QueueWorker plugin.
 *
 * @see \Drupal\Core\Queue\QueueWorkerBase
 * @see \Drupal\Core\Queue\QueueWorkerManager
 * @see \Drupal\Core\Annotation\QueueWorker
 * @see plugin_api
 */
interface ParStatisticInterface extends PluginInspectionInterface {

  /**
   * Render the statistic.
   */
  public function renderStat(): ?array;

}
