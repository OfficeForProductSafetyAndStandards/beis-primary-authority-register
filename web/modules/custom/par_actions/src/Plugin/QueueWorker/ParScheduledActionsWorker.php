<?php

namespace Drupal\par_actions\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\par_actions\ParActionsException;

/**
* A cron worker to process long running actions.
*
* @QueueWorker(
*   id = "par_scheduled_actions",
*   title = @Translation("PAR Scheduler Queue"),
*   cron = {"time" = 60}
* )
*/
class ParScheduledActionsWorker extends QueueWorkerBase {

  /**
   * Get the action manager service.
   *
   * @return mixed
   */
  public function getActionManager() {
    return \Drupal::service('plugin.manager.action');
  }

  /**
   * Create an instance of a given action plugin.
   *
   * @param $plugin_id
   * @param array $configuration
   *
   * @return mixed
   */
  public function getActionPlugin($plugin_id, $configuration = []) {
    return $this->getActionManager()->createInstance($plugin_id, $configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    if (!isset($data['action']) || !isset($data['entity'])) {
      return;
    }

    $configuration = isset($data['configuration']) ? $data['configuration'] : [];

    $action = $this->getActionPlugin($data['action'], $configuration);
    if (empty($action)) {
      throw new ParActionsException("The {$data['action']} action could not be loaded.");
    }

    try {
      $action->execute($data['entity']);
    }
    catch (\Exception $e) {
      throw new ParActionsException("The {$data['action']} action could not be processed.");
    }
  }

}
