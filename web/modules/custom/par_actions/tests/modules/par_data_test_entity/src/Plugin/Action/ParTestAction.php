<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * A dummy test action.
 *
 * @Action(
 *   id = "par_test_action",
 *   label = @Translation("A dummy test action"),
 *   type = "system"
 * )
 */
class ParTestAction extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    // Perform no action.
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
