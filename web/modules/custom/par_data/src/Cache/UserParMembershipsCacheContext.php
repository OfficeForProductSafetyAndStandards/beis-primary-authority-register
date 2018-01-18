<?php

namespace Drupal\par_data\Cache;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CalculatedCacheContextInterface;
use Drupal\Core\Cache\Context\UserCacheContextBase;

/**
 * Defines the UserParMembershipsCacheContext service, for "per membership" caching.
 *
 * Only use this cache context when checking explicitly whether a user is in a
 * given membership entity (e.g. authority or organisation).
 *
 * Cache context ID: 'user.par_memberships' (to vary by all par membership types).
 * Calculated cache context ID: 'user.par_memberships:%membership_type', e.g. 'user.par_membership:authority'
 * (to vary by a specific membership type only).
 */
class UserParMembershipsCacheContext extends UserCacheContextBase implements CalculatedCacheContextInterface {

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return t("User's par memberships");
  }

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($type = NULL) {
    $entities = [];
    $account = user_load($this->user->id());

    if ($type === 'authority' || $type === NULL) {
      $authorities = $this->getParDataManager()->isMemberOfAuthority($account);

      foreach ($authorities as $authority) {
        $entities[] = $authority->uuid();
      }
    }

    if ($type === 'organisation' || $type === NULL) {
      $organisations = $this->getParDataManager()->isMemberOfOrganisation($account);

      foreach ($organisations as $organisation) {
        $entities[] = $organisation->uuid();
      }
    }

    if (!empty($entities)) {
      return implode(',', $entities);
    }
    else {
      return '0';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($type = NULL) {
    return (new CacheableMetadata())->setCacheTags(['user:' . $this->user->id()]);
  }

}
