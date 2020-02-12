<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Revoke an entity.
 *
 * @Action(
 *   id = "par_action_revoke",
 *   label = @Translation("Revoke an entity"),
 *   type = "system"
 * )
 */
class ParRevokeAction extends ActionBase {

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

      if ($entity instanceof ParDataEntityInterface && $entity->getTypeEntity()->isRevokable()) {
        $reason = 'Automatically revoked by a scheduled rule.';
        $entity->revoke(TRUE, $reason);
      }

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
