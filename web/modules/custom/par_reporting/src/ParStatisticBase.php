<?php

namespace Drupal\par_reporting;

use Drupal\Component\Plugin\PluginBase;

/**
 * Provides a base implementation for a ParSchedule plugin.
 *
 * @see \Drupal\par_reporting\ParScheduleInterface
 * @see \Drupal\par_reporting\ParStatisticManager
 * @see \Drupal\par_reporting\Annotation\ParStatistic
 * @see plugin_api
 */
abstract class ParStatisticBase extends PluginBase implements ParStatisticInterface {

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
    return $this->pluginDefinition['status'] ?? TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getStaleness() {
    return $this->pluginDefinition['staleness'] ?? 3600;
  }

  /**
   * Simple getter to inject the PAR Reporting Manager service.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  public function getReportingManager() {
    return \Drupal::service('par_reporting.manager');
  }

  /**
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * Simple getter to inject the Entity Type Manager service.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager() {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * Get the statistic.
   */
  protected function getStat(): int {
    return 0;
  }

  /**
   * {@inheritDoc}
   */
  public function renderStat(): ?array {
    // Loading the statistic through the statistic manager adds the caching layer.
    $stat = $this->getReportingManager()->get($this->getPluginId());

    // Format a human-readable version of the statistic.
    $precision_intervals = [
      'b' => 1000000000,
      'm' => 1000000,
      'k' => 1000,
    ];
    foreach ($precision_intervals as $letter => $interval) {
      if ((int) $stat > $interval) {
        $whole = floor((10 * $stat) / $interval) / 10;
        $stat = sprintf('%.1f%s', $whole, "$letter");
        break;
      }
    }

    return [
      '#theme' => 'gds_data',
      '#attributes' => ['class' => 'govuk-grid-column-one-third'],
      '#value' => $stat,
      '#label' => $this->getTitle(),
      '#description' => $this->getDescription(),
    ];
  }

}
