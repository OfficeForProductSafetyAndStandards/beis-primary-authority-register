<?php

namespace Drupal\par_actions;

use Drupal\Component\Plugin\PluginBase;

/**
 * Provides a base implementation for a ParSchedule plugin.
 *
 * @see \Drupal\par_actions\ParScheduleInterface
 * @see \Drupal\par_actions\ParScheduleManager
 * @see \Drupal\par_actions\Annotation\ParSchedulerRule
 * @see plugin_api
 */
abstract class ParSchedulerRuleBase extends PluginBase implements ParSchedulerRuleInterface {

  /**
   * {@inheritdoc}
   */
  public function getItems() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildQueue() {

  }

}
