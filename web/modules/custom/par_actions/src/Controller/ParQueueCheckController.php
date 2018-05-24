<?php
/**
 * @file
 * Contains \Drupal\test_api\Controller\TestAPIController.
 */

namespace Drupal\par_actions\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller routines for test_api routes.
 */
class ParQueueCheckController extends ControllerBase {

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\Queue\QueueWorkerManagerInterface
   */
  protected $queueWorker;

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * The page cache kill switch.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a new HealthController object.
   *
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $kill_switch
   *   The page cache kill switch.
   */
  public function __construct(KillSwitch $kill_switch, QueueWorkerManagerInterface $queue_worker, QueueFactory $queue_factory, StateInterface $state) {
    $this->killSwitch = $kill_switch;
    $this->queueWorker = $queue_worker;
    $this->queueFactory = $queue_factory;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('page_cache_kill_switch'),
      $container->get('plugin.manager.queue_worker'),
      $container->get('queue'),
      $container->get('state')
    );
  }

  /**
   * Identify which queues still have items in them.
   */
  public function checkQueues(Request $request) {
    // Disable page cache
    $this->killSwitch->trigger();

    $timestamp = $this->state->get('system.cron_last');
    $time = new DrupalDateTime();
    $time->setTimestamp($timestamp);

    // The response should contain the queues found and the time of the last cron run.
    $response['data'] = [
      'queues' => [],
      'cron' => $time->format('Y-m-d H:i:s'),
      'cron_timestamp' => $timestamp,
    ];

    foreach ($this->queueWorker->getDefinitions() as $name => $queue_definition) {
      // Let's only list the PAR queues.
      if (substr($name, 0, 4 ) !== "par_") {
        continue;
      }

      $queue = $this->queueFactory->get($name);
      $response['data']['queues'][$name] = [
        'title' => (string) $queue_definition['title'],
        'number_items' => $queue->numberOfItems(),
      ];
    }

    return new JsonResponse($response);
  }
}
