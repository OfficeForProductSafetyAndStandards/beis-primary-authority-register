<?php

namespace Drupal\par_actions\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Provides base functionality for the ParEnforcementNoticeAutoApproval Queue Workers.
*/
abstract class ParEnforcementNoticeAutoApproval extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * Creates a new ParEnforcementNoticeAutoApproval object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   */
  public function __construct(EntityStorageInterface $node_storage) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity.manager')->getStorage('node')
    );
  }

  /**
  * {@inheritdoc}
  */
  public function processItem($data) {
    $notice = ParDataEnforcementNotice::load($data->id);

    if ($notice->areEnforcementActionsAllAwaitingApproval()) {
      foreach ($notice->getEnforcementActions() as $enforcement_action) {
        $enforcement_action->approve();
        \Drupal::logger('par_actions')->notice("{$notice->id()} approving {$enforcement_action->id()}");
      }
    }

    return $data->id;
  }

}
