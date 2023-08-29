<?php

namespace Drupal\par_tfa_sms\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\tfa\TfaLoginTrait;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Provides access control on the SMS verification form.
 *
 * @package Drupal\par_tfa_sms\Controller
 */
class ParTfaSmsLoginController {

  use TfaLoginTrait;

  /**
   * Checks that current user is selected user or is admin.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   The route to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function accessSelfOrAdmin(RouteMatchInterface $route, AccountInterface $account) {
    $target_user = $route->getParameter('user');

    // Start with a positive access result that can be cached based on the
    // current route, which includes both route name and parameters.
    $access = AccessResult::allowed();
    $access->addCacheContexts(['route']);

    if (!$target_user instanceof UserInterface) {
      return $access->andIf(AccessResult::forbidden('Invalid user.'));
    }

    // Before we perform any checks that are dependent on the current user, make
    // the result dependent on the current user. If we were just checking perms
    // here, we could rely on user.permissions, but in this case we are also
    // dependent on the ID of the user, which requires the higher level user
    // context.
    $access->addCacheableDependency($account);

    if (!$account->isAuthenticated()) {
      return $access->andIf(AccessResult::forbidden('User is not logged in.'));
    }

    // ID may be numeric string depending on entity class/storage, despite docs
    // for both AccountInterface::id() and UserInterface::id() claiming strict
    // integer.
    $is_self = (int) $account->id() === (int) $target_user->id();
    if (!$is_self) {
      $method = $route->getParameter('method');
      if (!empty($method)) {
        $plugin = \Drupal::service('plugin.manager.tfa')->createInstance($method, ['uid' => $target_user->id()]);
        if (method_exists($plugin, 'allowUserSetupAccess')) {
          $ret = $plugin->allowUserSetupAccess($route, $account);
          if ($ret === FALSE) {
            return $access->andIf(AccessResult::forbidden("Access denied for $method plugin."));
          }
        }
      }
    }

    $is_admin = $account->hasPermission('administer tfa for other users');
    $is_self_or_admin = AccessResult::allowedIf($is_self || $is_admin);

    return $access->andIf($is_self_or_admin);
  }

}
