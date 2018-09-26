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
 *   id = "par_entity_approve",
 *   label = @Translation("Approve par entities"),
 *   type = "system"
 * )
 */
class ParApproveEntity extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataEntityInterface) {
      // Always make auto-updates as the default admin.
      $approval_notes = "Automatically approved following the required statutory notice period.";
      $entity->setRevisionUserId(1);
      $entity->approve($approval_notes);
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
