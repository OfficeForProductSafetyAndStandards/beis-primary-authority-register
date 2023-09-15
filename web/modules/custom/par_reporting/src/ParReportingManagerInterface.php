<?php

namespace Drupal\par_reporting;

/**
* Interface for the Par Reporting Manager.
*/
interface ParReportingManagerInterface {

  /**
   * A helper method to run any plugin instance.
   *
   * @param string $id
   *   The ParStatistic plugin ID.
   *
   * @return ?array
   *   A rendered Statistic plugin.
   */
  public function render(string $id): ?array;

  /**
   * A helper method to get the value of any given stat.
   *
   * @param string $id
   *   The ParStatistic plugin ID.
   *
   * @return int
   *   The value for a Statistic plugin.
   */
  public function get(string $id): int;

}
