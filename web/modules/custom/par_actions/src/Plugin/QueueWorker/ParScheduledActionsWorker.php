<?php

namespace Drupal\par_actions\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

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
    return \Drupal::service('action_manager');
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
    var_dump($data['action']); die('asdfasdf');
    if (!isset($data['action']) || !isset($data['entity'])) {
      return FALSE;
    }

    $configuration = isset($data['configuration']) ? $data['configuration'] : [];

    $action = $this->getActionPlugin($data['action'], $configuration);

    var_dump($action->getPluginId());

    return $action->execute($data['entity']);
  }

}
