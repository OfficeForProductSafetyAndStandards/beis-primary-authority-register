<?php

namespace Drupal\par_actions;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for a QueueWorker plugin.
 *
 * @see \Drupal\Core\Queue\QueueWorkerBase
 * @see \Drupal\Core\Queue\QueueWorkerManager
 * @see \Drupal\Core\Annotation\QueueWorker
 * @see plugin_api
 */
interface ParSchedulerRuleInterface extends PluginInspectionInterface {

  /**
   * Adds items to the queue.
   */
  public function getItems();

  /**
   * Runs the plugin.
   */
  public function run();

  /**
   * Adds all unprocessed items into the queue.
   */
  public function buildQueue();

}
