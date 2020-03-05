<?php

namespace Drupal\par_data_test_entity\Plugin\test_plugins;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_actions\ParSchedulerRuleBase;

/**
 * Mock implementation of a scheduler rule for testing.
 */
class ParExpiryTest extends ParSchedulerRuleBase {

  protected $currentTime = 'now';

  /**
   * Retrieve the current test time.
   */
  protected function getCurrentTime() {
    return new DrupalDateTime($this->currentTime);
  }

  /**
   * Retrieve the current test time.
   *
   * Allows tests to modify the current time they run at.
   */
  public function setCurrentTime($time) {
    return $this->currentTime = $time;
  }

}
