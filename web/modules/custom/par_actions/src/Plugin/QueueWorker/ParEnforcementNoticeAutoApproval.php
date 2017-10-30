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
  * {@inheritdoc}
  */
  public function processItem($data) {
    $notice = ParDataEnforcementNotice::load($data->id);

    if ($notice->areEnforcementActionsAllAwaitingApproval() && $notice->isAutomaticApprovalDate()) {
      foreach ($notice->getEnforcementActions() as $enforcement_action) {
        $enforcement_action->approve();
        \Drupal::logger('par_actions')
          ->notice("{$notice->id()} approving {$enforcement_action->id()}");
      }
    }

    return $data->id;
  }

}
