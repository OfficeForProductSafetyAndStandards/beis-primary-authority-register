<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
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

  public function getCurrentUser() {
    return \Drupal::currentUser();
  }

  public function getAccountSwitcher() {
    return \Drupal::service('account_switcher');
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataEntityInterface) {
      // We need to make sure this is always run as the annonymous user,
      // cron will do this by default but for instances when this action
      // is run from outside cron we need to make sure to switch accounts.
      if (!$this->getCurrentUser()->isAnonymous()) {
        $accountSwitcher = $this->getAccountSwitcher();
        $accountSwitcher->switchTo(new AnonymousUserSession());
      }

      // Always make auto-updates as the default admin.
      $approval_notes = "Automatically approved following the required statutory notice period.";
      $entity->approve($approval_notes);

      if (isset($accountSwitcher) && $this->getCurrentUser()->isAnonymous()) {
        $accountSwitcher->switchBack();
      }
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
