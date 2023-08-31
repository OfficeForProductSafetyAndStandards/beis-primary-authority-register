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
   * Denies access unless user matches hash value.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   The route to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current logged in user, if any.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access(RouteMatchInterface $route, AccountInterface $account) {
    // Start with a positive access check which is cacheable for the current
    // route, which includes both route name and parameters.
    $access = AccessResult::allowed();
    $access->addCacheContexts(['route']);

    // Use uids here instead of user objects to prevent enumeration attacks.
    $uid = (int) $route->getParameter('uid');

    // If stored uid is invalid, the sms process didn't start in this session.
    $temp_store = \Drupal::service('tempstore.private')->get('par_tfa_sms');
    $uid_check = $temp_store->get('par-tfa-sms-entry-uid');
    if (!is_numeric($uid_check) || ($uid !== (int) $uid_check)) {
      return $access->andIf(AccessResult::forbidden('Invalid session.'));
    }
    else {
      $metadata = $temp_store->getMetadata('par-tfa-sms-entry-uid');
      $updated = is_null($metadata) ? 0 : $metadata->getUpdated();
      // Deny access, after 5 minutes since the start of the tfa process.
      if ($updated < (time() - 300)) {
        $temp_store->delete('par-tfa-sms-entry-uid');
        return $access->andIf(AccessResult::forbidden('Timeout expired.'));
      }
    }

    // Attempt to retrieve a user from the uid.
    /** @var \Drupal\user\UserInterface $user */
    $user = User::load($uid);
    if (!$user instanceof UserInterface) {
      return $access->andIf(AccessResult::forbidden('Invalid user.'));
    }

    // Since we're about to check the login hash, which is based on properties
    // of the user, we now need to vary the cache based on the user object.
    $access->addCacheableDependency($user);
    // If the login hash doesn't match, forbid access.
    if ($this->getLoginHash($user) !== $route->getParameter('hash')) {
      return $access->andIf(AccessResult::forbidden('Invalid hash value.'));
    }

    // If we've gotten here, we need to check that the current user is allowed
    // to use TFA SMS features for this account. To make this decision, we need to
    // vary the cache based on the current user.
    $access->addCacheableDependency($account);
    if ($account->isAuthenticated()) {
      return $access->andIf($this->accessSelfOrAdmin($route, $account));
    }

    return $access;
  }

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
