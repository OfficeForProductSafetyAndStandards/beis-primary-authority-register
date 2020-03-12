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
    $time = $this->pluginDefinition['time'];

    // If using our custom 'working days' relative time format convert this to
    // a php supported time format before processing.
    return $this->countWorkingDays() ?
      preg_replace('/working day/', 'day', $time) : $time;
  }

  /**
   * Whether only working days should be counted.
   *
   * {@inheritdoc}
   */
  public function countWorkingDays() {
    $time = $this->pluginDefinition['time'];
    // If the relative time format contains '+1 working day(s)'.
    return (preg_match('/^[+-][\d]+ working days?$/', $time) === 1);
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
   * Helper function to retrieve the current DateTime.
   *
   * Allows tests to modify the current time.
   */
  protected function getCurrentTime() {
    return new DrupalDateTime('now');
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $current_time = $this->getCurrentTime();
    // Base the scheduled time off the assigned current time.
    $scheduled_time = clone $current_time;
    $scheduled_time->modify($this->getTime());

    // We need to make additional calculations if counting working days only.
    if ($this->countWorkingDays()) {
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
      }
      else {
        $calculator->addBusinessDays($days);
      }

      // Replace default scheduled time with working day scheduled time.
      $scheduled_time = $calculator->getDate();
    }

    // The only supported operator at the moment is "<=" meaning that
    // the modified expiry (calculator) date provided by the 'time' property
    // must be greater than or equal to the entity's date property.
    // e.g. $entity->date <= $calculator->date
    $operator = '<=';

    $query = \Drupal::entityQuery($this->getEntity());
    $query->condition($this->getProperty(), $scheduled_time->format('Y-m-d'), $operator);

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
