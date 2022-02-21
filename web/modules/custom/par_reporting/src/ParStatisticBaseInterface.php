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
interface ParStatisticBaseInterface extends PluginInspectionInterface {

  /**
   * Important the value of a different statistic.
   *
   * @param string $stat
   *   The statistic plugin id for the stat to import.
   */
  public function importStat(string $stat): int;

  /**
   * Get the statistic.
   */
  public function getStat();

  /**
   * Render the statistic.
   */
  public function renderStat();

}
