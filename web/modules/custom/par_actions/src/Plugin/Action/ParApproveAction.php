<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Approves a par entity.
 *
 * @Action(
 *   id = "par_action_approve",
 *   label = @Translation("Approve par entities"),
 *   type = "system"
 * )
 */
class ParApproveAction extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataEntityInterface) {
      $entity->approve();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // @TODO Implement entity/action checks
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
