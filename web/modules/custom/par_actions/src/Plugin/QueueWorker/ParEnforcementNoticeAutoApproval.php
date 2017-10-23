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
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Creates a new NodePublishBase object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   */
  public function __construct(EntityStorageInterface $node_storage) {
    $this->nodeStorage = $node_storage;
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
  * Publishes a node.
  *
  * @param NodeInterface $node
  * @return int
  */
  protected function publishNode($node) {
    $node->setPublished(TRUE);
    return $node->save();
  }

  /**
  * {@inheritdoc}
  */
  public function processItem($data) {

    $notice = ParDataEnforcementNotice::load($data->id);
    drush_print($notice->get('notice_date')->getString());
    drush_print($notice->getEnforcementActionsStatuses());

//    if ($notice->getEnforcementActionsStatuses()) {
      foreach ($notice->getEnforcementActions() as $entity) {

        drush_print(print_r($entity, 1));
        drush_print($entity->id());
        drush_print(var_export($entity->approve(), 1));
      }
//    }

    drush_print($data->id);

    return $data->id;
  }

}
