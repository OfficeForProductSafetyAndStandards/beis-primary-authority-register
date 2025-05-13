<?php

namespace Drupal\par_actions\Plugin\Factory;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 *
 */
class BusinessDaysCalculator {

  const MONDAY    = 1;
  const TUESDAY   = 2;
  const WEDNESDAY = 3;
  const THURSDAY  = 4;
  const FRIDAY    = 5;
  const SATURDAY  = 6;
  const SUNDAY    = 7;

  /**
   * Constructs a BusinessDaysCalculator object.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   Date to start calculations from.
   * @param \Drupal\Core\Datetime\DrupalDateTime $holidays
   *   Array of holidays, holidays are not considered business days.
   * @param int $nonBusinessDays
   *   Array of days of the week which are not business days.
   */
  public function __construct(
    protected DrupalDateTime $date,
    /**
     * Array of holidays.
     */
    protected array $holidays,
    /**
     * Array of days of the week which are not business days.
     */
    protected array $nonBusinessDays,
  ) {
  }

  /**
   *
   */
  public function addBusinessDays($howManyDays) {
    $i = 0;
    while ($i < $howManyDays) {
      $this->date->modify("+1 day");
      if ($this->isBusinessDay($this->date)) {
        $i++;
      }
    }
  }

  /**
   *
   */
  public function removeBusinessDays($howManyDays) {
    $i = 0;
    while ($i < $howManyDays) {
      $this->date->modify("-1 day");
      if ($this->isBusinessDay($this->date)) {
        $i++;
      }
    }
  }

  /**
   *
   */
  public function getDate() {
    return $this->date;
  }

  /**
   *
   */
  private function isBusinessDay(DrupalDateTime $date) {
    if (in_array((int) $date->format('N'), $this->nonBusinessDays)) {
      // Date is a nonBusinessDay.
      return FALSE;
    }
    foreach ($this->holidays as $day) {
      $day = new DrupalDateTime($day, 'UTC');
      if ($date->format('Y-m-d') == $day->format('Y-m-d')) {
        // Date is a holiday.
        return FALSE;
      }
    }
    // Date is a business day.
    return TRUE;
  }

}
