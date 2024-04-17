<?php

namespace Drupal\par_member_upload_flows;

use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Interface for the Par Member CSV Handler.
 */
interface ParMemberCsvHandlerInterface {

  /**
   * Process a CSV file.
   *
   * @param \Drupal\file\FileInterface $file
   * @param array $rows
   *   An array to add processed rows to.
   *
   * @return array
   *   An array of row data.
   */
  public function loadFile(FileInterface $file, array &$rows);

  /**
   * Save data to a CSV file.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The partnership to generate a name for.
   * @param array $rows
   *   An array to add processed rows to.
   *
   * @return bool|FileInterface
   *   Return the file if successfully saved, otherwise return false.
   */
  public function saveFile(ParDataPartnership $par_data_partnership, array $rows): bool|FileInterface;

  /**
   * Get the column headings for this CSV file.
   *
   * @param bool $processed
   *   Whether to include processed properties.
   *   Processed properties should not be entered by users and therefore
   *   should be excluded from all user validation.
   */
  public function getColumns(bool $processed = TRUE);

  /**
   * Lock the partnership member lock.
   *
   * @return bool
   */
  public function lock(ParDataPartnership $par_data_partnership);

  /**
   * Unlock the partnership member lock.
   *
   * @return bool
   */
  public function unlock(ParDataPartnership $par_data_partnership);

  /**
   * Validates a given CSV row.
   *
   * @return ParCsvViolation[]|null
   */
  public function validate(array $rows);

  //
  //  /**
  //   * Process the CSV.
  //   *
  //   * @return bool
  //   */
  //  public function process();
  //
  //  /**
  //   * Cleanup tasks required before the batch completes.
  //   *
  //   * @return bool
  //   */
  //  public function cleanup();
  //
  //  /**
  //   * Completion tasks once all data has been imported.
  //   *
  //   * @return bool
  //   */
  //  public function complete();
}
