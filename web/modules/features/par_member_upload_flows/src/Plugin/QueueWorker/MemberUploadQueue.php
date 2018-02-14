<?php

/**
 * @file
 * Contains \Drupal\par_member_upload_flows\Plugin\QueueWorker\MemberUploadQueue.
 */

namespace Drupal\par_member_upload_flows\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes Tasks.
 *
 * @QueueWorker(
 *   id = "par_member_upload",
 *   title = @Translation("Member Upload: Import csv data queue"),
 *   cron = {"time" = 60}
 * )
 */
class MemberUploadQueue extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   *
   * @todo Process csv data via cron.
   */
  public function processItem($data) {
    
  }

}
