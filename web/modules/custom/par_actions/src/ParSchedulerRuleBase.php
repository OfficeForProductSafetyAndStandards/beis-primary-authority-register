<?php

namespace Drupal\par_actions;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\par_actions\Plugin\Factory\BusinessDaysCalculator;
use Drupal\par_data\ParDataManagerInterface;
use RapidWeb\UkBankHolidays\Factories\UkBankHolidayFactory;

/**
 * Provides a base implementation for a ParSchedule plugin.
 *
 * @see \Drupal\par_actions\ParScheduleInterface
 * @see \Drupal\par_actions\ParScheduleManager
 * @see \Drupal\par_actions\Annotation\ParSchedulerRule
 * @see plugin_api
 */
abstract class ParSchedulerRuleBase extends PluginBase implements ParSchedulerRuleInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->pluginDefinition['weight'];
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->pluginDefinition['status'];
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    return $this->pluginDefinition['entity'];
  }

  /**
   * {@inheritdoc}
   */
  public function getProperty() {
    return $this->pluginDefinition['property'];
  }

  /**
   * {@inheritdoc}
   */
  public function getTime() {
    return $this->pluginDefinition['time'];
  }

  /**
   * {@inheritdoc}
   */
  public function getQueue() {
    return $this->pluginDefinition['queue'];
  }

  /**
   * {@inheritdoc}
   */
  public function getAction() {
    return $this->pluginDefinition['action'];
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
  public function query() {
    $current_time = new DrupalDateTime('now');
    // Don't use this time, this is just to work out if the time is
    // greater or lesser than the scheduled time.
    $scheduled_time = new DrupalDateTime($this->getTime());

    // Find date to process.
    $holidays = array_column(UkBankHolidayFactory::getAll(), 'date', 'date');

    $calculator = new BusinessDaysCalculator(
      $current_time,
      $holidays,
      [BusinessDaysCalculator::SATURDAY, BusinessDaysCalculator::SUNDAY]
    );

    // Calculate the constituent parts based on the relative time diff.
    $diff = $current_time->diff($scheduled_time);
    $days = $diff->format("%a");
    if ($diff->invert) {
      $calculator->removeBusinessDays($days);
      $operator = '<=';
    }
    else {
      $calculator->addBusinessDays($days);
      $operator = '<=';
    }

    $query = \Drupal::entityQuery($this->getEntity());
    $query->condition($this->getProperty(), $calculator->getDate()->format('Y-m-d'), $operator);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getItems() {
    $results = $this->query()->execute();
    $storage = $this->getParDataManager()->getEntityTypeStorage($this->getEntity());

    return $results ? $storage->loadMultiple($results) : [];
  }

  /**
   * Run the rule plugins action.
   *
   * Some actions will run immediately, some will run from a queue.
   */
  public function run() {
    if ($this->getQueue()) {
      $this->buildQueue();
    }
    else {
      $action = $this->getActionPlugin($this->getAction());
      $entities = $this->getItems();
      foreach ($entities as $entity) {
        $action->execute($entity);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildQueue() {
    /** @var QueueFactory $queue_factory */
    $queue_factory = \Drupal::service('queue');
    /** @var QueueInterface $queue */
    $queue = $queue_factory->get('par_scheduled_actions');

    $entities = $this->getItems();
    foreach ($entities as $entity) {
      $item = [
        'rule' => $this->getPluginId(),
        'entity' => $entity,
        'action' => $this->getAction(),
      ];
      $queue->createItem($item);
    }
  }

}
