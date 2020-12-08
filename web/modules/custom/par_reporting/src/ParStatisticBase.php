<?php

namespace Drupal\par_reporting;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a base implementation for a ParSchedule plugin.
 *
 * @see \Drupal\par_reporting\ParScheduleInterface
 * @see \Drupal\par_reporting\ParStatisticManager
 * @see \Drupal\par_reporting\Annotation\ParStatistic
 * @see plugin_api
 */
abstract class ParStatisticBase extends PluginBase implements ParStatisticBaseInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['title'] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->pluginDefinition['weight'] ?? 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->pluginDefinition['status'] ?? true;
  }

  /**
   * {@inheritdoc}
   */
  public function getStaleness() {
    return $this->pluginDefinition['staleness'] ?? 3600;
  }

  /**
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * Simple getter to inject the Entity Type Manager service.
   *
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager() {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * Get the statistic
   *
   * {@inheritDoc}
   */
  public function getStat() {
    return 0;
  }

  /**
   * {@inheritDoc}
   */
  public function renderStat() {
    return [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'column-one-third'],
      '#value' => $this->getStat(),
      '#label' => $this->getTitle(),
      '#description' => $this->getDescription(),
    ];
  }

}
