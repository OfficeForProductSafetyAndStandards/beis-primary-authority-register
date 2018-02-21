<?php

namespace Drupal\par_member_upload_flows;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\file\FileInterface;

/**
 * Interface for the Par Member CSV Handler.
 */
interface ParMemberCsvHandlerInterface {

  /**
   * Process a CSV file
   *
   * @param FileInterface $file
   * @param array $rows
   *   An array to add processed rows to.
   * @param boolean $skip
   *   Whether to skip the headers.
   *
   * @return array
   *   An array of row data.
   */
  public function loadFile(FileInterface $file, array $rows, bool $skip = TRUE);

  /**
   * Get the column headings for this CSV file.
   */
  public function getColumns();

  /**
   * Get the heading for a given column.
   */
  public function getColumnsByIndex(int $index);

  /**
   * Lock the partnership member lock.
   *
   * @return bool
   */
  public function lock();

  /**
   * Unlock the partnership member lock.
   *
   * @return bool
   */
  public function unlock();

  /**
   * Validates a given CSV row.
   *
   * @return bool
   */
  public function validateRow();

  /**
   * Process the CSV.
   *
   * @return bool
   */
  public function process();

  /**
   * Cleanup tasks required before the batch completes.
   *
   * @return bool
   */
  public function cleanup();

  /**
   * Completion tasks once all data has been imported.
   *
   * @return bool
   */
  public function complete();
}
